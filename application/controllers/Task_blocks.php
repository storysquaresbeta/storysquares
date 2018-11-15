<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Task_blocks extends MY_Controller {

    function __construct() {
        parent::__construct();
        //$this->access_only_admin();
    }

    function index() {
        $this->template->rander("task_blocks/index");
    }
    
    private function can_manage_all_projects() {
        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_all_projects") == "1") {
            return true;
        }
    }
    
    private function can_create_task_blocks($in_project = true) {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_create_task_blocks") == "1") {
                //check is user a project member
                if($in_project){
                     return $this->is_user_a_project_member; //check the specific project permission
                }else{
                   return true;
                }
                
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_create_task_blocks")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_edit_task_blocks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_edit_task_blocks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_edit_task_blocks")) {
                return $this->is_clients_project;
            }
        }
    }

    private function can_delete_task_blocks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_task_blocks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_delete_task_blocks")) {
                return $this->is_clients_project;
            }
        }
    }
    
     //When checking project permissions, to reduce db query we'll use this init function, where team members has to be access on the project
    private function init_project_permission_checker($project_id = 0) {
        if ($this->login_user->user_type == "client") {
            $project_info = $this->Projects_model->get_one($project_id);
            if ($project_info->client_id == $this->login_user->client_id) {
                $this->is_clients_project = true;
            }
        } else {
            $this->is_user_a_project_member = $this->Project_members_model->is_user_a_project_member($project_id, $this->login_user->id);
        }
    }
    
    private function can_view_task_blocks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_task_blocks")) {
                //even the settings allow to view milestones, the client can only create their own project's milestones
                return $this->is_clients_project;
            }
        }
    }

    function blocks($project_id) {
        $this->init_project_permission_checker($project_id);

        if (!$this->can_view_task_blocks()) {
            redirect("forbidden");
        }
        
        $view_data["can_create_task_blocks"] = $this->can_create_task_blocks();
        $view_data["can_edit_task_blocks"] = $this->can_edit_task_blocks();
        $view_data["can_delete_task_blocks"] = $this->can_delete_task_blocks();

        $view_data['project_id'] = $project_id;


        $this->load->view("task_blocks/index", $view_data);
    }
    
    function modal_form() {
        $id = $this->input->post('id');
        validate_submitted_data(array(
            "id" => "numeric",
            "project_id" => "numeric"
        ));
        
        $view_data['model_info'] = $this->Task_blocks_model->get_one($this->input->post('id'));
       
        $project_id = $this->input->post('project_id') ? $this->input->post('project_id') : $view_data['model_info']->project_id;

        $this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_task_blocks()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_task_blocks()) {
                redirect("forbidden");
            }
        }

        $view_data['project_id'] = $project_id;

        
        $this->load->view('task_blocks/modal_form', $view_data);
    }

    function save() {
        validate_submitted_data(array(
            "id" => "numeric",
            "project_id" => "numeric",
            "title" => "required"
        ));


        $id = $this->input->post('id');
        $data = array(
            "project_id" => $this->input->post('project_id'),
            "title" => $this->input->post('title'),
            "color" => $this->input->post('color'),
            "created_by" => $this->login_user->id,
            "created_at" => get_current_utc_time(),
        );
        
        $project_id = $this->input->post('project_id');

        $this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_task_blocks()) {
                redirect("forbidden");
            }
        } else {
            if (!$this->can_create_task_blocks()) {
                redirect("forbidden");
            }
        }

        if (!$id) {
            //get sort value
            $max_sort_value = $this->Task_blocks_model->get_max_sort_value();
            $data["sort"] = $max_sort_value * 1 + 1; //increase sort value
        }

        $save_id = $this->Task_blocks_model->save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    //update the sort value for the fields
    function update_field_sort_values($id = 0) {

        $sort_values = $this->input->post("sort_values");
        if ($sort_values) {

            //extract the values from the comma separated string
            $sort_array = explode(",", $sort_values);


            //update the value in db
            foreach ($sort_array as $value) {
                $sort_item = explode("-", $value); //extract id and sort value

                $id = get_array_value($sort_item, 0);
                $sort = get_array_value($sort_item, 1);

                $data = array("sort" => $sort);
                $this->Task_blocks_model->save($data, $id);
            }
        }
    }

    function delete() {
        validate_submitted_data(array(
            "id" => "required|numeric"
        ));
        $id = $this->input->post('id');
        $info = $this->Task_blocks_model->get_one($id);
        $this->init_project_permission_checker($info->project_id);

        if (!$this->can_delete_task_blocks()) {
            redirect("forbidden");
        }
        

        if ($this->input->post('undo')) {
            if ($this->Task_blocks_model->delete_task_blocks_and_sub_items($id)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Task_blocks_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    
    function list_data($project_id) {
        $options = array("project_id" => $project_id);
        
        $list_data = $this->Task_blocks_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Task_blocks_model->get_details($options)->row();
        return $this->_make_row($data);
    }

    private function _make_row($data) {

        $delete = "";
        $edit = "";

        if (!$data->key_name) {
            $edit = modal_anchor(get_uri("task_blocks/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_task_blocks'), "data-post-id" => $data->id));
            $delete = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_task_blocks'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("task_blocks/delete"), "data-action" => "delete-confirmation"));
        }

        return array(
            $data->sort,
            "<div class='pt10 pb10 field-row'  data-id='$data->id'><i class='fa fa-bars pull-left' style='margin: 3px 30px 0 0; cursor:s-resize; opacity:0.3'></i> <span style='background-color:" . $data->color . "' class='color-tag  pull-left'></span>" . ($data->key_name ? lang($data->key_name) : $data->title) . '</div>',
            $edit . $delete
        );
    }

}

/* End of file task_blocks.php */
/* Location: ./application/controllers/task_blocks.php */