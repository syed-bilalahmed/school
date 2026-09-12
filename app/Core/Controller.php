<?php
class Controller {
    // Load Model
    public function model($model){
        // Require model file
        if(file_exists('../app/Models/' . $model . '.php')){
            require_once '../app/Models/' . $model . '.php';
            // Instantiate model
            return new $model();
        } else {
            die("Model $model does not exist");
        }
    }

    // Load View
    public function view($view, $data = []){
        // Automatically inject system currency if not explicitly provided
        if (!isset($data['currency'])) {
            if (empty($_SESSION['currency_symbol'])) {
                try {
                    if (file_exists('../app/Models/SiteSetting.php')) {
                        require_once '../app/Models/SiteSetting.php';
                        $ss = new SiteSetting();
                        $_SESSION['currency_symbol'] = $ss->getSetting('currency_symbol', 'PKR');
                    }
                } catch (Exception $e) {
                    $_SESSION['currency_symbol'] = 'PKR';
                }
            }
            $data['currency'] = !empty($_SESSION['currency_symbol']) ? $_SESSION['currency_symbol'] : 'PKR';
        }

        // Check for view file
        if(file_exists('../app/Views/' . $view . '.php')){
            require_once '../app/Views/' . $view . '.php';
        } else {
            // View does not exist
            die("View does not exist");
        }
    }
}
