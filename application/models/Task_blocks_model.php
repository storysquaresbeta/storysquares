<?php

class Task_blocks_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'task_blocks';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $task_blocks_table = $this->db->dbprefix('task_blocks');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $task_blocks_table.id=$id";
        }
        
        $project_id = get_array_value($options, "project_id");        
        if ($project_id) {
            $where = " AND $task_blocks_table.project_id=$project_id";
        }

        $sql = "SELECT $task_blocks_table.*
        FROM $task_blocks_table
        WHERE $task_blocks_table.deleted=0 $where
        ORDER BY $task_blocks_table.sort ASC";
        return $this->db->query($sql);
    }

    function get_max_sort_value() {
        $task_blocks_table = $this->db->dbprefix('task_blocks');

        $sql = "SELECT MAX($task_blocks_table.sort) as sort
        FROM $task_blocks_table
        WHERE $task_blocks_table.deleted=0";
        $result = $this->db->query($sql);
        if ($result->num_rows()) {
            return $result->row()->sort;
        } else {
            return 0;
        }
    }
    
    function get_max_sort_value_by_project($project_id) {
        $task_blocks_table = $this->db->dbprefix('task_blocks');

        $sql = "SELECT MAX($task_blocks_table.sort) as sort
        FROM $task_blocks_table
        WHERE $task_blocks_table.deleted=0 and project_id = $project_id";
        $result = $this->db->query($sql);
        if ($result->num_rows()) {
            return $result->row()->sort;
        } else {
            return 0;
        }
    }
    
    function delete_task_blocks_and_sub_items($task_block_id) {
        $task_blocks_table = $this->db->dbprefix('task_blocks');
        $tasks_table = $this->db->dbprefix('tasks');


        //delete the project and sub items
        $delete_task_block_sql = "UPDATE $task_blocks_table SET $task_blocks_table.deleted=1 WHERE $task_blocks_table.id=$task_block_id; ";
        $this->db->query($delete_task_block_sql);

        $delete_tasks_sql = "UPDATE $tasks_table SET $tasks_table.deleted=1 WHERE $tasks_table.task_block_id=$task_block_id; ";
        $this->db->query($delete_tasks_sql);

        return true;
    }

}
