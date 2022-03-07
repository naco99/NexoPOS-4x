<?php

namespace App\Crud;

use App\Exceptions\NotAllowedException;
use App\Models\Role;
use App\Services\CrudService;
use App\Services\Helper;
use App\Services\Users;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use TorMorten\Eventy\Facades\Events as Hook;

class RolesCrud extends CrudService
{
	/**
	 * define the base table
	 */
	protected string $table = 'nexopos_roles';
	
	/**
	 * base route name
	 */
	protected string $mainRoute = 'ns.roles';
	
	/**
	 * Define namespace
	 * @param string
	 */
	protected string $namespace = 'ns.roles';
	
	/**
	 * Model Used
	 */
	protected string $model = Role::class;
	
	/**
	 * Adding relation
	 */
	public array $relations = [
	];
	
	/**
	 * Pick
	 * Restrict columns you retreive from relation.
	 * Should be an array of associative keys, where
	 * keys are either the related table or alias name.
	 * Example : [
	 *      'user'  =>  [ 'username' ], // here the relation on the table nexopos_users is using "user" as an alias
	 * ]
	 */
	public array $pick = [];
	
	/**
	 * Define where statement
	 * @var  array
	 **/
	protected array $listWhere = [];
	
	/**
	 * Define where in statement
	 * @var  array
	 */
	protected array $whereIn = [];
	
	/**
	 * Fields which will be filled during post/put
	 */
	public array $fillable = [];
	
	protected array $permissions = [
		'create' => 'create.roles',
		'read' => 'read.roles',
		'update' => 'update.roles',
		'delete' => 'delete.roles',
	];
	
	/**
	 * Define Constructor
	 * @param
	 */
	public function __construct()
	{
		parent::__construct();
		
		Hook::addFilter($this->namespace . '-crud-actions', [$this, 'setActions'], 10, 2);
	}
	
	/**
	 * Return the label used for the crud
	 * instance
	 * @return  array
	 **/
	public function getLabels(): array
	{
		return [
			'list_title' => __('Roles List'),
			'list_description' => __('Display all roles.'),
			'no_entry' => __('No role has been registered.'),
			'create_new' => __('Add a new role'),
			'create_title' => __('Create a new role'),
			'create_description' => __('Create a new role and save it.'),
			'edit_title' => __('Edit role'),
			'edit_description' => __('Modify  Role.'),
			'back_to_list' => __('Return to Roles'),
		];
	}
	
	/**
	 * Check whether a feature is enabled
	 * @param $feature
	 * @return  boolean
	 */
	public function isEnabled($feature): bool
	{
		return false; // by default
	}
	
	/**
	 * Fields
	 * @param object/null
	 * @return  array of field
	 */
	public function getForm($entry = null): array
	{
		return [
			'main' => [
				'label' => __('Name'),
				'name' => 'name',
				'value' => $entry->name ?? '',
				'description' => __('Provide a name to the role.')
			],
			'tabs' => [
				'general' => [
					'label' => __('General'),
					'fields' => [
						[
							'type' => 'text',
							'name' => 'namespace',
							'label' => __('Namespace'),
							'validation' => $entry === null ? 'required|unique:nexopos_roles,namespace' : [
								'required',
								Rule::unique('nexopos_roles', 'namespace')->ignore($entry->id)
							],
							'description' => __('Should be a unique value with no spaces or special character'),
							'value' => $entry->namespace ?? '',
						], [
							'type' => 'select',
							'name' => 'dashid',
							'label' => __('Dashboard Identifier'),
							'validation' => 'required',
							'options' => Helper::kvToJsOptions(Hook::filter('ns-dashboard-identifiers', [
								'store' => __('Store Dashboard'),
								'cashier' => __('Cashier Dashboard'),
								'default' => __('Default Dashboard'),
							])),
							'description' => __('Define what should be the home page of the dashboard.'),
							'value' => $entry->dashid ?? '',
						], [
							'type' => 'textarea',
							'name' => 'description',
							'label' => __('Description'),
							'description' => __('Provide more details about what this role is about.'),
							'value' => $entry->description ?? '',
						],
					]
				]
			]
		];
	}
	
	/**
	 * Filter POST input fields
	 * @param array of fields
	 * @return  array of fields
	 */
	public function filterPostInputs($inputs): array
	{
		$inputs['namespace'] = Str::slug($inputs['namespace']);
		$inputs['locked'] = false;
		return $inputs;
	}
	
	/**
	 * Filter PUT input fields
	 * @param array of fields
	 * @return  array of fields
	 */
	public function filterPutInputs($inputs): array
	{
		$inputs['namespace'] = Str::slug($inputs['namespace']);
		return $inputs;
	}
	
	/**
	 * Before saving a record
	 * @param Request $request
	 * @return  Request
	 * @throws NotAllowedException
	 */
	public function beforePost(Request $request): Request
	{
		$this->allowedTo('create');
		
		return $request;
	}
	
	/**
	 * After saving a record
	 * @param Request $request
	 * @param Role $entry
	 * @return  Request
	 */
	public function afterPost($request, Role $entry): Request
	{
		return $request;
	}
	
	/**
	 * get
	 * @param string
	 * @return ?string
	 */
	public function get($param): ?string
	{
		if($param == 'model'){
			return $this->model;
		}
		
		return null;
	}
	
	/**
	 * Before updating a record
	 * @param Request $request
	 * @param $entry
	 * @return  Request
	 * @throws NotAllowedException
	 */
	public function beforePut(Request $request, $entry): Request
	{
		$this->allowedTo('update');
		
		return $request;
	}
	
	/**
	 * After updating a record
	 * @param Request $request
	 * @param object entry
	 * @return  Request
	 */
	public function afterPut(Request $request, $entry): Request
	{
		return $request;
	}
	
	/**
	 * Protect an access to a specific crud UI
	 * @param array { namespace, id, type }
	 * @return  array
	 * @throws Exception
	 */
	public function canAccess($fields): array
	{
		$users = app()->make(Users::class);
		
		if ($users->is(['admin'])) {
			return [
				'status' => 'success',
				'message' => __('The access is granted.')
			];
		}
		
		throw new Exception(__('You don\'t have access to that ressource'));
	}
	
	/**
	 * Before Delete
	 * @return  void
	 * @throws NotAllowedException|Exception
	 */
	public function beforeDelete($namespace, $id, $model)
	{
		if ($namespace == 'ns.roles') {
			$this->allowedTo('delete');
			
			if ($model->locked) {
				throw new Exception(__('Unable to delete a system role.'));
			}
		}
	}
	
	/**
	 * Define Columns
	 * @return  array of columns configuration
	 */
	public function getColumns(): array
	{
		return [
			'name' => [
				'label' => __('Name'),
				'$direction' => '',
				'$sort' => false
			],
			'namespace' => [
				'label' => __('Namespace'),
				'$direction' => '',
				'$sort' => false
			],
			'created_at' => [
				'label' => __('Created At'),
				'$direction' => '',
				'$sort' => false
			],
		];
	}
	
	/**
	 * Define actions
	 */
	public function setActions($entry, $namespace)
	{
		// Don't overwrite
		$entry->{'$checked'} = false;
		$entry->{'$toggled'} = false;
		$entry->{'$id'} = $entry->id;
		$entry->locked = ( bool )$entry->locked;
		// you can make changes here
		$entry->{'$actions'} = [
			[
				'label' => __('Edit'),
				'namespace' => 'edit',
				'type' => 'GOTO',
				'index' => 'id',
				'url' => ns()->url('/dashboard/' . 'users/roles' . '/edit/' . $entry->id)
			], [
				'label' => __('Clone'),
				'namespace' => 'clone',
				'type' => 'GET',
				'confirm' => [
					'message' => __('Would you like to clone this role ?'),
				],
				'index' => 'id',
				'url' => ns()->url('/api/nexopos/v4/' . 'users/roles/' . $entry->id . '/clone')
			], [
				'label' => __('Delete'),
				'namespace' => 'delete',
				'type' => 'DELETE',
				'url' => ns()->url('/api/nexopos/v4/crud/ns.roles/' . $entry->id),
				'confirm' => [
					'message' => __('Would you like to delete this ?'),
				]
			]
		];
		
		return $entry;
	}
	
	
	/**
	 * Bulk Delete Action
	 * @param Request $request
	 * @throws Exception
	 *  @return JsonResponse|int[]|void
	 */
	public function bulkAction(Request $request)
	{
		/**
		 * Deleting licence is only allowed for admin
		 * and supervisor.
		 */
		$user = app()->make(Users::class);
		if (!$user->is(['admin', 'supervisor'])) {
			return response()->json([
				'status' => 'failed',
				'message' => __('You\'re not allowed to do this operation')
			], 403);
		}
		
		if ($request->input('action') == 'delete_selected') {
			
			ns()->restrict(
				['delete.roles'],
				__('You do not have enough permissions to perform this action.')
			);
			
			$status = [
				'success' => 0,
				'failed' => 0
			];
			
			foreach ($request->input('entries') as $id) {
				$entity = $this->model::find($id);
				
				/**
				 * make sure system roles can't be deleted
				 */
				if ($entity instanceof Role) {
					if ($entity->locked) {
						$status['failed']++;
					} else {
						$entity->delete();
						$status['success']++;
					}
				} else {
					$status['failed']++;
				}
			}
			return $status;
		}
		
		return Hook::filter($this->namespace . '-catch-action', false, $request);
	}
	
	/**
	 * get Links
	 * @return  array of links
	 */
	public function getLinks(): array
	{
		return [
			'list' => ns()->url('dashboard/' . 'users/roles'),
			'create' => ns()->url('dashboard/' . 'users/roles/create'),
			'edit' => ns()->url('dashboard/' . 'users/roles/edit/{id}'),
			'post' => ns()->url('api/nexopos/v4/crud/' . 'ns.roles'),
			'put' => ns()->url('api/nexopos/v4/crud/' . 'ns.roles/{id}' . ''),
		];
	}
	
	/**
	 * Get Bulk actions
	 * @return  array of actions
	 **/
	public function getBulkActions(): array
	{
		return Hook::filter($this->namespace . '-bulk', [
			[
				'label' => __('Delete Selected Groups'),
				'identifier' => 'delete_selected',
				'url' => ns()->route('ns.api.crud-bulk-actions', [
					'namespace' => $this->namespace
				])
			]
		]);
	}
	
	/**
	 * get exports
	 * @return  array of export formats
	 **/
	public function getExports(): array
	{
		return [];
	}
}