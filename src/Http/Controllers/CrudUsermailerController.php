<?php

namespace IlBronza\Mailer\Http\Controllers;

use IlBronza\CRUD\CRUD;
use IlBronza\CRUD\Traits\CRUDBelongsToManyTrait;
use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDDeleteTrait;
use IlBronza\CRUD\Traits\CRUDDestroyTrait;
use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDNestableTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;
use IlBronza\CRUD\Traits\CRUDUpdateEditorTrait;
use IlBronza\Mailer\Models\Usermailer;
use Illuminate\Http\Request;

class CrudUsermailerController extends CRUD
{
    use CRUDShowTrait;
    use CRUDPlainIndexTrait;
    use CRUDIndexTrait;
    use CRUDEditUpdateTrait;
    use CRUDUpdateEditorTrait;
    use CRUDCreateStoreTrait;

    use CRUDDeleteTrait;
    use CRUDDestroyTrait;

    use CRUDRelationshipTrait;
    use CRUDBelongsToManyTrait;

    use CRUDNestableTrait;


    public static $tables = [
        'index' => [
            'fields' => 
            [
                'mySelfEdit' => 'links.edit',
                'mySelfSee' => 'links.see',
                'user' => 'relations.belongsTo',
                'transport' => 'flat',
                'host' => 'flat',
                'encryption' => 'flat',
                'username' => 'flat',
                'password' => 'flat',
                'timeout' => 'flat',
                'auth_mode' => 'flat'
            ]
        ]
    ];

    static $formFields = [
        'common' => [
            'default' => [
                'user' => [
                    'type' => 'select',
                    'multiple' => false,
                    'rules' => 'required|exists:users,id',
                    'relation' => 'user'
                ],
                'transport' => ['text' => 'string|nullable'],
                'host' => ['text' => 'string|nullable'],
                'encryption' => ['text' => 'string|nullable'],
                'username' => ['text' => 'string|required'],
                'password' => ['text' => 'string|nullable'],
                'timeout' => ['text' => 'string|nullable'],
                'auth_mode' => ['text' => 'string|nullable']
            ]
        ]
    ];    

    /**
     * subject model class full path
     **/
    public $modelClass = Usermailer::class;

    /**
     * http methods allowed. remove non existing methods to get a 403
     **/
    public $allowedMethods = [
        'index',
        'show',
        'edit',
        'update',
        'create',
        'store',
        'destroy',
        'deleted',
        'archived',
        'reorder',
        'storeReorder'
    ];

    /**
     * to override show view use full view name
     **/
    //public $showView = 'products.showPartial';

    // public $guardedEditDBFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
    // public $guardedCreateDBFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
    // public $guardedShowDBFields = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * relations called to be automatically shown on 'show' method
     **/
    //public $showMethodRelationships = ['posts', 'users', 'operations'];

    /**
        protected $relationshipsControllers = [
        'permissions' => '\IlBronza\AccountManager\Http\Controllers\PermissionController'
    ];
    **/


    /**
     * getter method for 'index' method.
     *
     * is declared here to force the developer to rationally choose which elements to be shown
     *
     * @return Collection
     **/

    public function getIndexElements()
    {
        return Usermailer::all();
    }

    /**
     * parameter that decides which fields to use inside index table
     **/
    //  public $indexFieldsGroups = ['index'];

    /**
     * parameter that decides if create button is available
     **/
    //  public $avoidCreateButton = true;



    /**
     * START base methods declared in extended controller to correctly perform dependency injection
     *
     * these methods are compulsorily needed to execute CRUD base functions
     **/
    public function show(Usermailer $usermailer)
    {
        //$this->addExtraView('top', 'folder.subFolder.viewName', ['some' => $thing]);

        return $this->_show($usermailer);
    }

    public function edit(Usermailer $usermailer)
    {
        return $this->_edit($usermailer);
    }

    public function update(Request $request, Usermailer $usermailer)
    {
        return $this->_update($request, $usermailer);
    }

    public function destroy(Usermailer $usermailer)
    {
        return $this->_destroy($usermailer);
    }

    /**
     * END base methods
     **/




     /**
      * START CREATE PARAMETERS AND METHODS
      **/

    // public function beforeRenderCreate()
    // {
    //     $this->modelInstance->agent_id = session('agent')->getKey();
    // }

     /**
      * STOP CREATE PARAMETERS AND METHODS
      **/

}

