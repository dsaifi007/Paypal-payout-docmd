<?php
/**
Auther - Chromeinfotech
Description -- This class use for handle the users basic information
Version -- 3.0.0
*/
class User_model extends CI_Model {
 
    protected $patient_table = 'patient_info';
    protected $user_table = "users";
    protected $user_patient = "user_patient";
    protected $address_mapping = "patient_address";
    protected $address = "address";

    protected $column_order = array(null,"users.id",null, 'ptnt.first_name','ptnt.last_name','ptnt.gender','ptnt.date_of_birth','users.email','users.phone','users.is_blocked','users.id'); 
    //set column field database for datatable orderable
    protected $column_search = array(null,'ptnt.first_name','ptnt.last_name','ptnt.gender','ptnt.date_of_birth','users.email','users.phone','users.is_blocked','users.id'); 
    //set column field database for datatable searchable 
    protected $order = array('ptnt.id' => 'asc'); // default order 
    protected $emails = [];
    public function __construct()
    {
        parent::__construct();
    }
 
    private function _get_datatables_query($filter_data = null)
    {
        if ($filter_data != null) {
         $where_cond = "city ='".$filter_data['city']."' OR state = '".$filter_data['state']."'  OR  gender = '".$filter_data['gender']."'  OR  health_insurance ='".$filter_data['health_insurance']."'";
         $this->db->where($where_cond);
        }    
        $this->db->select(
                "users.id,
                ptnt.first_name,
                ptnt.last_name,
                ptnt.gender,
                ptnt.date_of_birth,
                users.email,
                users.phone,
                users.is_blocked"
                ); 
        $this->db->from("$this->user_table as users");
        $this->db->join("$this->user_patient as user_patient","user_patient.user_id = users.id","LEFT");
        $this->db->join("$this->patient_table as ptnt","user_patient.patient_id = ptnt.id","INNER");
        $i = 0;
     
        foreach ($this->column_search as $item) // loop column 
        {
            if(@$_POST['search']['value']) // if datatable send POST for search
            {
                 
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
         
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
 
    function get_datatables($filter =null)
    {
        $this->_get_datatables_query($filter = null);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all()
    {
        $this->db->from($this->patient_table);
        return $this->db->count_all_results();
    }
    public function update_user_status_model($user_data)
    {
        $this->db->where("id",$user_data['user_id']);
        $this->db->update($this->user_table,["is_blocked" => $user_data['status']]);
        return true;
    }
    public function get_all_emails($user_id = null)
    {
        if($user_id != null) {
            $this->db->where_in("id",$user_id);
        }
        $this->db->select("email")->from($this->user_table);
        $query = $this->db->get();
        foreach ($query->result_array() as $key => $value) {
            $this->emails[] = trim($value['email']);
        }
        return $this->emails;
    }
    // get user information based on id
    public function get_user_data($user_id)
    {
        $this->db->where("user_patient.user_id",$user_id);
        $this->db->select(
                "users.id,
                CONCAT(ptnt.first_name,' ',ptnt.last_name) AS name,
                ptnt.gender,
                ptnt.date_of_birth,
                users.email,
                users.phone,
                ptnt.profile_url,
                address.address,
                address.city,
                address.state,
                address.zip_code,
                ptnt.provider,
                ptnt.member_id,
                ptnt.ins_group,
                users.is_blocked"
                ); 
        $this->db->from("$this->user_table as users");
        $this->db->join("$this->user_patient as user_patient","user_patient.user_id = users.id","LEFT");
        $this->db->join("$this->patient_table as ptnt","user_patient.patient_id = ptnt.id","INNER");
        $this->db->join("$this->address_mapping as address_mapping","address_mapping.patient_id = ptnt.id","INNER");
        $this->db->join("$this->address as address","address.id = address_mapping.address_id","INNER");
        $query = $this->db->get();
        return $query->row_array();
        //echo "ddd".$this->db->last_query();die;
    }
}

?>