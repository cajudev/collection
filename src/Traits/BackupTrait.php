<?php

namespace Cajudev\Traits;

trait BackupTrait
{
    /**
     * Create a backup of the content of array
     */   
    public function backup() {
        $this->backup = $this->content;
    }

    /**
     * Restore the data of the array
     */
    public function restore() {
        $this->content = $this->backup;
        unset($this->backup);
        $this->count();
    }
}