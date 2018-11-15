<?php

class Checklist_items_model extends Crud_model {

    private $table = null;

    function __construct() {
        $this->table = 'checklist_items';
        parent::__construct($this->table);
    }
    
    function get_all_checklist_of_project($project_id){
        $checklist_items_table = $this->db->dbprefix('checklist_items');
        $tasks_table = $this->db->dbprefix('tasks');
 
        $sql = "SELECT $checklist_items_table.task_id, $checklist_items_table.title
        FROM $checklist_items_table
        LEFT JOIN $tasks_table ON $tasks_table.id = $checklist_items_table.task_id 
        WHERE $checklist_items_table.deleted=0 AND $tasks_table.project_id = $project_id";
        return $this->db->query($sql);
    }
    
}