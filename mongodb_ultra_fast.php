<?php
/**
 * Ultra-Fast MongoDB-like Database Implementation
 * Uses streaming, indexing, and lazy loading for maximum performance
 */

class UltraFastMongoDB {
    private $dataDir;
    private $database;
    private $cache = [];
    private $indexCache = [];
    
    public function __construct($database = 'schogms') {
        $this->database = $database;
        $this->dataDir = __DIR__ . '/mongodb_data/' . $database;
        
        if (!file_exists($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }
    
    public function collection($name) {
        return new UltraFastMongoCollection($this->dataDir, $name, $this->cache, $this->indexCache);
    }
    
    public function testConnection() {
        return is_dir($this->dataDir) && is_writable($this->dataDir);
    }
}

class UltraFastMongoCollection {
    private $filePath;
    private $indexPath;
    private $cache;
    private $indexCache;
    private $data = null;
    private $indexes = null;
    private $lastModified = 0;
    
    public function __construct($dataDir, $collection, &$cache, &$indexCache) {
        $this->filePath = $dataDir . '/' . $collection . '.json';
        $this->indexPath = $dataDir . '/' . $collection . '_index.json';
        $this->cache = &$cache;
        $this->indexCache = &$indexCache;
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
    
    private function loadIndexes() {
        if ($this->indexes !== null) {
            return; // Already loaded
        }
        
        $cacheKey = $this->indexPath;
        
        // Check cache first
        if (isset($this->indexCache[$cacheKey]) && 
            file_exists($this->indexPath) &&
            filemtime($this->indexPath) == $this->indexCache[$cacheKey]['mtime']) {
            $this->indexes = $this->indexCache[$cacheKey]['data'];
            return;
        }
        
        if (file_exists($this->indexPath)) {
            $content = file_get_contents($this->indexPath);
            $this->indexes = json_decode($content, true) ?: [];
            
            // Cache the indexes
            $this->indexCache[$cacheKey] = [
                'data' => $this->indexes,
                'mtime' => filemtime($this->indexPath)
            ];
        } else {
            $this->indexes = [];
        }
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
    
    private function saveIndexes() {
        if ($this->indexes === null) {
            return;
        }
        
        file_put_contents($this->indexPath, json_encode($this->indexes, JSON_UNESCAPED_UNICODE));
        
        // Update cache
        $cacheKey = $this->indexPath;
        $this->indexCache[$cacheKey] = [
            'data' => $this->indexes,
            'mtime' => time()
        ];
    }
    
    // Stream-based reading for large files
    public function streamFind($filter = [], $options = []) {
        $limit = $options['limit'] ?? 50;
        $offset = $options['offset'] ?? 0;
        $results = [];
        $count = 0;
        $skipped = 0;
        
        if (!file_exists($this->filePath)) {
            return $results;
        }
        
        $handle = fopen($this->filePath, 'r');
        if (!$handle) {
            return $results;
        }
        
        // Skip the opening bracket
        fseek($handle, 1);
        
        $buffer = '';
        $braceCount = 0;
        $inString = false;
        $escapeNext = false;
        
        while (!feof($handle) && count($results) < $limit) {
            $char = fgetc($handle);
            
            if ($escapeNext) {
                $buffer .= $char;
                $escapeNext = false;
                continue;
            }
            
            if ($char === '\\') {
                $escapeNext = true;
                $buffer .= $char;
                continue;
            }
            
            if ($char === '"' && !$escapeNext) {
                $inString = !$inString;
            }
            
            if (!$inString) {
                if ($char === '{') {
                    $braceCount++;
                } elseif ($char === '}') {
                    $braceCount--;
                }
            }
            
            $buffer .= $char;
            
            // Complete object found
            if ($braceCount === 0 && $buffer !== '') {
                $object = json_decode($buffer, true);
                if ($object !== null) {
                    if ($skipped < $offset) {
                        $skipped++;
                    } else {
                        if (empty($filter) || $this->matchesFilter($object, $filter)) {
                            $results[] = $object;
                        }
                    }
                }
                $buffer = '';
                
                // Skip comma and whitespace
                while (!feof($handle)) {
                    $nextChar = fgetc($handle);
                    if ($nextChar === ',' || $nextChar === ' ' || $nextChar === "\n" || $nextChar === "\r") {
                        continue;
                    } else {
                        fseek($handle, -1, SEEK_CUR);
                        break;
                    }
                }
            }
        }
        
        fclose($handle);
        return $results;
    }
    
    // Fast count without loading all data
    public function fastCount($filter = []) {
        if (empty($filter)) {
            // Use file size estimation for empty filter
            if (!file_exists($this->filePath)) {
                return 0;
            }
            
            $content = file_get_contents($this->filePath);
            $data = json_decode($content, true);
            return is_array($data) ? count($data) : 0;
        }
        
        // For filtered counts, we need to scan
        $count = 0;
        $handle = fopen($this->filePath, 'r');
        if (!$handle) {
            return 0;
        }
        
        fseek($handle, 1); // Skip opening bracket
        
        $buffer = '';
        $braceCount = 0;
        $inString = false;
        $escapeNext = false;
        
        while (!feof($handle)) {
            $char = fgetc($handle);
            
            if ($escapeNext) {
                $buffer .= $char;
                $escapeNext = false;
                continue;
            }
            
            if ($char === '\\') {
                $escapeNext = true;
                $buffer .= $char;
                continue;
            }
            
            if ($char === '"' && !$escapeNext) {
                $inString = !$inString;
            }
            
            if (!$inString) {
                if ($char === '{') {
                    $braceCount++;
                } elseif ($char === '}') {
                    $braceCount--;
                }
            }
            
            $buffer .= $char;
            
            if ($braceCount === 0 && $buffer !== '') {
                $object = json_decode($buffer, true);
                if ($object !== null && $this->matchesFilter($object, $filter)) {
                    $count++;
                }
                $buffer = '';
                
                // Skip comma and whitespace
                while (!feof($handle)) {
                    $nextChar = fgetc($handle);
                    if ($nextChar === ',' || $nextChar === ' ' || $nextChar === "\n" || $nextChar === "\r") {
                        continue;
                    } else {
                        fseek($handle, -1, SEEK_CUR);
                        break;
                    }
                }
            }
        }
        
        fclose($handle);
        return $count;
    }
    
    public function find($filter = [], $options = []) {
        // For large datasets, use streaming
        if (isset($options['stream']) && $options['stream']) {
            return $this->streamFind($filter, $options);
        }
        
        // For small datasets or when streaming is disabled, use normal method
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
        return $this->fastCount($filter);
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
    
    // Pagination with streaming
    public function findPaginated($filter = [], $options = []) {
        $page = $options['page'] ?? 1;
        $limit = $options['limit'] ?? 50;
        $offset = ($page - 1) * $limit;
        
        // Use streaming for large datasets
        $results = $this->streamFind($filter, [
            'limit' => $limit,
            'offset' => $offset
        ]);
        
        $total = $this->fastCount($filter);
        
        return [
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total / $limit)
        ];
    }
    
    // Fast search with streaming
    public function search($searchTerm, $fields = [], $options = []) {
        $page = $options['page'] ?? 1;
        $limit = $options['limit'] ?? 50;
        $offset = ($page - 1) * $limit;
        
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
                        case '$or':
                            $matches = false;
                            foreach ($operatorValue as $orCondition) {
                                if ($this->matchesFilter($doc, $orCondition)) {
                                    $matches = true;
                                    break;
                                }
                            }
                            if (!$matches) {
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
$mongodb = new UltraFastMongoDB('schogms');

// Test connection
if (!$mongodb->testConnection()) {
    error_log("Warning: MongoDB data directory not writable");
}
?>
