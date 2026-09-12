<?php
class Setting {
    private $db;
    private $siteSettingModel;

    public function __construct(){
        $this->db = new Database();
        if(!class_exists('SiteSetting')){
            require_once APPROOT . '/Models/SiteSetting.php';
        }
        $this->siteSettingModel = new SiteSetting();
    }

    public function getSettings(){
        $settingsAry = $this->siteSettingModel->getAllSettings();
        $obj = (object)$settingsAry;

        // Populate consistent standard property fallbacks
        if(!isset($obj->school_name) || empty($obj->school_name)) {
            $obj->school_name = 'City Model High School & College';
        }
        if(!isset($obj->campus_name) || empty($obj->campus_name)) {
            $obj->campus_name = 'Main Executive Campus';
        }
        $obj->email = $obj->school_email ?? 'info@citymodelschool.edu.pk';
        $obj->phone = $obj->school_phone ?? '+92-51-111-222-333';
        $obj->address = $obj->school_address ?? 'Plot 45-B, Sector H-8/4, Education City, Islamabad';
        $obj->currency_symbol = $obj->currency_symbol ?? 'Rs.';
        $obj->logo = $obj->logo ?? '';

        return $obj;
    }

    public function updateSettings($data){
        return $this->siteSettingModel->updateSettings($data);
    }

    // Session Management
    public function getSessions(){
        return $this->siteSettingModel->getSessions();
    }

    public function addSession($session){
        return $this->siteSettingModel->addSession($session);
    }
    
    public function deleteSession($id){
        return $this->siteSettingModel->deleteSession($id);
    }
}
