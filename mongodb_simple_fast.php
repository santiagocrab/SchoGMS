<?php
/**
 * Simple Fast MongoDB-like Database Implementation
 * Optimized for performance without timeouts
 */

class SimpleFastMongoDB {
    private $dataDir;
    private $database;
    private $cache = [];
    
    public function __construct($database = 'schogms') {
        $this->database = $database;
        $this->dataDir = __DIR__ . '/mongodb_data/' . $database;
        
        if (!file_exists($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }
    
    public function collection($name) {
        return new SimpleFastMongoCollection($this->dataDir, $name, $this->cache);
    }
    
    public function testConnection() {
        return is_dir($this->dataDir) && is_writable($this->dataDir);
    }
}

class SimpleFastMongoCollection {
    private $filePath;
    private $cache;
    private $data = null;
    private $lastModified = 0;
    
    public function __construct($dataDir, $collection, &$cache) {
        $this->filePath = $dataDir . '/' . $collection . '.json';
        $this->cache = &$cache;
    }
    
    private function loadData() {
        if ($this->data !== null) {
            return; // Already loaded
        }
        
        $cacheKey = $this->filePath;
        
        // Check cache first
        if (isset($this->cache[$cacheKey]) && 
            file_exists($this->filePath) &&
            filemtime($this->filePath) == $this->cache[$cacheKey]['mtime']) {
            $this->data = $this->cache[$cacheKey]['data'];
            return;
        }
        
        if (file_exists($this->filePath)) {
            $this->lastModified = filemtime($this->filePath);
            $content = file_get_contents($this->filePath);
            $this->data = json_decode($content, true) ?: [];
            
            // Cache the data
            $this->cache[$cacheKey] = [
                'data' => $this->data,
                'mtime' => $this->lastModified
            ];
        } else {
            $this->data = [];
        }
    }
    
    public function clearCache() {
        $this->data = null;
        $cacheKey = $this->filePath;
        unset($this->cache[$cacheKey]);
    }
    
    private function saveData() {
        if ($this->data === null) {
            return;
        }
        
        $json = json_encode($this->data, JSON_UNESCAPED_UNICODE);
        file_put_contents($this->filePath, $json);
        
        // Update cache
        $cacheKey = $this->filePath;
        $this->cache[$cacheKey] = [
            'data' => $this->data,
            'mtime' => time()
        ];
        
        $this->lastModified = time();
    }
    
    public function find($filter = [], $options = []) {
        $this->loadData();
        $results = $this->data;
        
        // Apply filter
        if (!empty($filter)) {
            $results = array_filter($results, function($doc) use ($filter) {
                return $this->matchesFilter($doc, $filter);
            });
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
    
    public function count($filter = []) {
        $this->loadData();
        
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
    
    public function insertOne($document) {
        $this->loadData();
        
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
    
    public function updateOne($filter, $update, $options = []) {
        $this->loadData();
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
                break;
            }
        }
        
        if ($modifiedCount > 0) {
            $this->saveData();
        }
        
        return ['modifiedCount' => $modifiedCount];
    }
    
    public function deleteOne($filter) {
        $this->loadData();
        $deletedCount = 0;
        
        foreach ($this->data as $index => $doc) {
            if ($this->matchesFilter($doc, $filter)) {
                array_splice($this->data, $index, 1);
                $deletedCount = 1;
                break;
            }
        }
        
        if ($deletedCount > 0) {
            $this->saveData();
        }
        
        return ['deletedCount' => $deletedCount];
    }
    
    public function deleteMany($filter) {
        $this->loadData();
        $deletedCount = 0;
        
        // Work backwards to avoid index issues when deleting
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
    
    // Simple pagination
    public function findPaginated($filter = [], $options = []) {
        $page = $options['page'] ?? 1;
        $limit = $options['limit'] ?? 50;
        $offset = ($page - 1) * $limit;
        
        $this->loadData();
        $results = $this->data;
        
        // Apply filter
        if (!empty($filter)) {
            $results = array_filter($results, function($doc) use ($filter) {
                return $this->matchesFilter($doc, $filter);
            });
        }
        
        $total = count($results);
        $results = array_slice($results, $offset, $limit);
        
        return [
            'data' => array_values($results),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }
    
    // Simple search
    public function search($searchTerm, $fields = [], $options = []) {
        $page = $options['page'] ?? 1;
        $limit = $options['limit'] ?? 50;
        $offset = ($page - 1) * $limit;
        
        $this->loadData();
        $results = [];
        
        foreach ($this->data as $doc) {
            $match = false;
            foreach ($fields as $field) {
                if (isset($doc[$field]) && stripos($doc[$field], $searchTerm) !== false) {
                    $match = true;
                    break;
                }
            }
            if ($match) {
                $results[] = $doc;
            }
        }
        
        $total = count($results);
        $results = array_slice($results, $offset, $limit);
        
        return [
            'data' => array_values($results),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }
    
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
}

// Create global MongoDB instance
$mongodb = new SimpleFastMongoDB('schogms');

// Test connection
if (!$mongodb->testConnection()) {
    error_log("Warning: MongoDB data directory not writable");
}
?>
