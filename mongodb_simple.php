<?php
/**
 * Simple MongoDB-like Database Implementation
 * This provides MongoDB-like functionality using JSON files
 */

class SimpleMongoDB {
    private $dataDir;
    private $database;
    
    public function __construct($database = 'schogms') {
        $this->database = $database;
        $this->dataDir = __DIR__ . '/mongodb_data/' . $database;
        
        if (!file_exists($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }
    
    public function collection($name) {
        return new SimpleMongoCollection($this->dataDir, $name);
    }
    
    public function testConnection() {
        return is_dir($this->dataDir) && is_writable($this->dataDir);
    }
}

class SimpleMongoCollection {
    private $filePath;
    private $data;
    
    public function __construct($dataDir, $collection) {
        $this->filePath = $dataDir . '/' . $collection . '.json';
        $this->loadData();
    }
    
    private function loadData() {
        if (file_exists($this->filePath)) {
            $content = file_get_contents($this->filePath);
            $this->data = json_decode($content, true) ?: [];
        } else {
            $this->data = [];
        }
    }
    
    private function saveData() {
        return file_put_contents($this->filePath, json_encode($this->data, JSON_PRETTY_PRINT));
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
        
        // Apply filter
        if (!empty($filter)) {
            $results = array_filter($results, function($doc) use ($filter) {
                return $this->matchesFilter($doc, $filter);
            });
        }
        
        // Apply sorting
        if (isset($options['sort'])) {
            $sortField = key($options['sort']);
            $sortDirection = $options['sort'][$sortField];
            
            usort($results, function($a, $b) use ($sortField, $sortDirection) {
                $valA = $a[$sortField] ?? '';
                $valB = $b[$sortField] ?? '';
                
                if ($sortDirection === -1) {
                    return $valB <=> $valA;
                } else {
                    return $valA <=> $valB;
                }
            });
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
        $updated = false;
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
                $updated = true;
                break; // Only update first match
            }
        }
        
        if ($updated) {
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
$mongodb = new SimpleMongoDB('schogms');

// Test connection
if (!$mongodb->testConnection()) {
    error_log("Warning: MongoDB data directory not writable");
}
?>
