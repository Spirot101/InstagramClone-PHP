<?php

if(loggedIn()){
    Redirect::to(url_for('index'));
  }
 
  if(Input::exists()){
    if(isset($_POST['submitButton'])){
        //initialize an array to store any error message from the form
        $form_errors=array();

        //Form validation
        $required_fields=array("email","password","username","full_name");
       
        //call the function to check empty field and merge the return data into form_error array
        $form_errors=array_merge($form_errors,check_empty_fields($required_fields));

        //Fields that requires checking for minimum length
        $fields_to_check_length=array("full_name"=>3,"username"=>3,"password"=>6);
       
        //call the function to check minimum required length and merge the return data into form_error array
        $form_errors=array_merge($form_errors,check_min_length($fields_to_check_length));

        //email validation / merge the return data into form_error array
        $form_errors=array_merge($form_errors,check_email($_POST));

        $rules=[
            'email'=>array('unique'=>'users'),
            'username'=>array('unique'=>'users'),
            'password'=>array('max'=>30)
        ];

        $account->check($_POST,$rules);

        if($account->passed()){
            //check if error array is empty, if yes process form data and insert record
             if(empty($form_errors)){
                   //collect form data and store in variables
                    $username=escape($_POST['username']);
                    $fullName=escape($_POST['full_name']);
                    $email=escape($_POST['email']);
                    $password=escape($_POST['password']);

                    $user_id=$account->register_user($username,$fullName,$email,$password);
                    if($user_id){
                        session_regenerate_id();
                        $_SESSION['user_id']=$user_id;
                        Redirect::to(url_for('index'));
                    }

            }
        }else{
            $form_errors=array_merge($form_errors,$account->errors());
        }
       
        
    }
  }
   $title="Register • Instagram";
   $keywords = "Instagram, Share and capture world's moments, share, capture, share, login, signup";

   ?>