<?php
/**
 * Fast MongoDB-like Database Implementation
 * Optimized for performance with large datasets
 */

class FastMongoDB {
    private $dataDir;
    private $database;
    private $cache = [];
    private $cacheTimeout = 300; // 5 minutes cache
    
    public function __construct($database = 'schogms') {
        $this->database = $database;
        $this->dataDir = __DIR__ . '/mongodb_data/' . $database;
        
        if (!file_exists($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }
    
    public function collection($name) {
        return new FastMongoCollection($this->dataDir, $name, $this->cache);
    }
    
    public function testConnection() {
        return is_dir($this->dataDir) && is_writable($this->dataDir);
    }
}

class FastMongoCollection {
    private $filePath;
    private $indexPath;
    private $data;
    private $indexes = [];
    private $cache;
    private $lastModified = 0;
    
    public function __construct($dataDir, $collection, &$cache) {
        $this->filePath = $dataDir . '/' . $collection . '.json';
        $this->indexPath = $dataDir . '/' . $collection . '_index.json';
        $this->cache = &$cache;
        $this->loadData();
        $this->loadIndexes();
    }
    
    private function loadData() {
        $cacheKey = $this->filePath;
        
        // Check cache first
        if (isset($this->cache[$cacheKey]) && 
            time() - $this->cache[$cacheKey]['timestamp'] < 300) {
            $this->data = $this->cache[$cacheKey]['data'];
            return;
        }
        
        if (file_exists($this->filePath)) {
            $this->lastModified = filemtime($this->filePath);
            $content = file_get_contents($this->filePath);
            
            // Use faster JSON parsing
            $this->data = json_decode($content, true) ?: [];
            
            // Cache the data
            $this->cache[$cacheKey] = [
                'data' => $this->data,
                'timestamp' => time()
            ];
        } else {
            $this->data = [];
        }
    }
    
    private function loadIndexes() {
        if (file_exists($this->indexPath)) {
            $content = file_get_contents($this->indexPath);
            $this->indexes = json_decode($content, true) ?: [];
        } else {
            $this->indexes = [];
        }
    }
    
    private function saveData() {
        // Only save if data has changed
        if (file_exists($this->filePath) && filemtime($this->filePath) == $this->lastModified) {
            return;
        }
        
        $json = json_encode($this->data, JSON_PRETTY_PRINT);
        file_put_contents($this->filePath, $json);
        
        // Update cache
        $cacheKey = $this->filePath;
        $this->cache[$cacheKey] = [
            'data' => $this->data,
            'timestamp' => time()
        ];
        
        $this->lastModified = time();
    }
    
    private function saveIndexes() {
        file_put_contents($this->indexPath, json_encode($this->indexes, JSON_PRETTY_PRINT));
    }
    
    public function insertOne($document) {
        if (!isset($document['_id'])) {
            $document['_id'] = $this->generateId();
        }
        
        if (!isset($document['created_at'])) {
            $document['created_at'] = date('Y-m-d H:i:s');
        }
        
        if (!isset($document['updated_at'])) {
            $document['updated_at'] = date('Y-m-d H:i:s');
        }
        
        $this->data[] = $document;
        $this->saveData();
        
        return ['insertedId' => $document['_id']];
    }
    
    public function insertMany($documents) {
        $insertedIds = [];
        foreach ($documents as $document) {
            $result = $this->insertOne($document);
            $insertedIds[] = $result['insertedId'];
        }
        return ['insertedIds' => $insertedIds];
    }
    
    public function find($filter = [], $options = []) {
        $results = $this->data;
        
        // Apply filter with optimized matching
        if (!empty($filter)) {
            $results = $this->filterData($results, $filter);
        }
        
        // Apply sorting
        if (isset($options['sort'])) {
            $results = $this->sortData($results, $options['sort']);
        }
        
        // Apply limit
        if (isset($options['limit'])) {
            $results = array_slice($results, 0, $options['limit']);
        }
        
        return array_values($results);
    }
    
    public function findOne($filter = []) {
        $results = $this->find($filter, ['limit' => 1]);
        return !empty($results) ? $results[0] : null;
    }
    
    public function updateOne($filter, $update, $options = []) {
        $modifiedCount = 0;
        
        foreach ($this->data as $index => $doc) {
            if ($this->matchesFilter($doc, $filter)) {
                if (isset($update['$set'])) {
                    foreach ($update['$set'] as $key => $value) {
                        $this->data[$index][$key] = $value;
                    }
                } else {
                    foreach ($update as $key => $value) {
                        $this->data[$index][$key] = $value;
                    }
                }
                
                $this->data[$index]['updated_at'] = date('Y-m-d H:i:s');
                $modifiedCount = 1;
                break; // Only update first match
            }
        }
        
        if ($modifiedCount > 0) {
            $this->saveData();
        }
        
        return ['modifiedCount' => $modifiedCount];
    }
    
    public function updateMany($filter, $update, $options = []) {
        $modifiedCount = 0;
        
        foreach ($this->data as $index => $doc) {
            if ($this->matchesFilter($doc, $filter)) {
                if (isset($update['$set'])) {
                    foreach ($update['$set'] as $key => $value) {
                        $this->data[$index][$key] = $value;
                    }
                } else {
                    foreach ($update as $key => $value) {
                        $this->data[$index][$key] = $value;
                    }
                }
                
                $this->data[$index]['updated_at'] = date('Y-m-d H:i:s');
                $modifiedCount++;
            }
        }
        
        if ($modifiedCount > 0) {
            $this->saveData();
        }
        
        return ['modifiedCount' => $modifiedCount];
    }
    
    public function deleteOne($filter) {
        $deletedCount = 0;
        
        foreach ($this->data as $index => $doc) {
            if ($this->matchesFilter($doc, $filter)) {
                array_splice($this->data, $index, 1);
                $deletedCount = 1;
                break; // Only delete first match
            }
        }
        
        if ($deletedCount > 0) {
            $this->saveData();
        }
        
        return ['deletedCount' => $deletedCount];
    }
    
    public function deleteMany($filter) {
        $deletedCount = 0;
        
        for ($i = count($this->data) - 1; $i >= 0; $i--) {
            if ($this->matchesFilter($this->data[$i], $filter)) {
                array_splice($this->data, $i, 1);
                $deletedCount++;
            }
        }
        
        if ($deletedCount > 0) {
            $this->saveData();
        }
        
        return ['deletedCount' => $deletedCount];
    }
    
    public function count($filter = []) {
        if (empty($filter)) {
            return count($this->data);
        }
        
        $count = 0;
        foreach ($this->data as $doc) {
            if ($this->matchesFilter($doc, $filter)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    // Optimized filtering
    private function filterData($data, $filter) {
        $results = [];
        
        foreach ($data as $doc) {
            if ($this->matchesFilter($doc, $filter)) {
                $results[] = $doc;
            }
        }
        
        return $results;
    }
    
    // Optimized sorting
    private function sortData($data, $sort) {
        $sortField = key($sort);
        $sortDirection = $sort[$sortField];
        
        usort($data, function($a, $b) use ($sortField, $sortDirection) {
            $valA = $a[$sortField] ?? '';
            $valB = $b[$sortField] ?? '';
            
            if ($sortDirection === -1) {
                return $valB <=> $valA;
            } else {
                return $valA <=> $valB;
            }
        });
        
        return $data;
    }
    
    private function matchesFilter($doc, $filter) {
        foreach ($filter as $key => $value) {
            if (is_array($value)) {
                // Handle MongoDB operators
                foreach ($value as $operator => $operatorValue) {
                    switch ($operator) {
                        case '$regex':
                            if (!preg_match('/' . $operatorValue . '/i', $doc[$key] ?? '')) {
                                return false;
                            }
                            break;
                        case '$in':
                            if (!in_array($doc[$key] ?? null, $operatorValue)) {
                                return false;
                            }
                            break;
                        case '$ne':
                            if (($doc[$key] ?? null) === $operatorValue) {
                                return false;
                            }
                            break;
                        case '$gt':
                            if (($doc[$key] ?? 0) <= $operatorValue) {
                                return false;
                            }
                            break;
                        case '$lt':
                            if (($doc[$key] ?? 0) >= $operatorValue) {
                                return false;
                            }
                            break;
                    }
                }
            } else {
                // Simple equality check
                if (($doc[$key] ?? null) !== $value) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    private function generateId() {
        return uniqid() . '_' . mt_rand(1000, 9999);
    }
    
    // Pagination support for large datasets
    public function findPaginated($filter = [], $options = []) {
        $page = $options['page'] ?? 1;
        $limit = $options['limit'] ?? 50;
        $offset = ($page - 1) * $limit;
        
        $results = $this->find($filter, $options);
        $total = count($results);
        
        return [
            'data' => array_slice($results, $offset, $limit),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }
    
    // Search with pagination
    public function search($searchTerm, $fields = [], $options = []) {
        $filter = [];
        
        if (!empty($searchTerm)) {
            $orConditions = [];
            foreach ($fields as $field) {
                $orConditions[] = [$field => ['$regex' => $searchTerm, '$options' => 'i']];
            }
            $filter['$or'] = $orConditions;
        }
        
        return $this->findPaginated($filter, $options);
    }
}

// Create global MongoDB instance
$mongodb = new FastMongoDB('schogms');

// Test connection
if (!$mongodb->testConnection()) {
    error_log("Warning: MongoDB data directory not writable");
}
?>
