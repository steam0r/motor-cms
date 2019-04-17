<?php

namespace Motor\CMS\Services;

use Motor\Backend\Services\BaseService;
use Motor\CMS\Models\PageVersionComponent;
use Motor\Media\Models\FileAssociation;

class ComponentBaseService extends BaseService
{

    public static function render(PageVersionComponent $pageComponent)
    {
        $container = app();

        $controller = $container->make(config('motor-cms-page-components.components.' . $pageComponent->component_name . '.component_class'),
            ['pageVersionComponent' => $pageComponent, 'component' => $pageComponent->component]);

        return $container->call([$controller, 'index']);

    }


    public static function createPageComponent($request)
    {
        // Create the page component
        $pageComponent                  = new PageVersionComponent();
        $pageComponent->page_version_id = $request->get('page_version_id');
        $pageComponent->container       = $request->get('container');
        $pageComponent->component_name  = $request->get('component_name');
        $pageComponent->sort_position   = PageVersionComponent::where('page_version_id',
                $request->get('page_version_id'))->where('container', $request->get('container'))->count() + 1;
        $pageComponent->save();
    }


    public function beforeCreate()
    {
    }


    public function afterCreate()
    {
        // Create the page component
        $pageComponent                  = new PageVersionComponent();
        $pageComponent->page_version_id = $this->request->get('page_version_id');
        $pageComponent->container       = $this->request->get('container');
        $pageComponent->component_name  = $this->name;
        $pageComponent->sort_position   = PageVersionComponent::where('page_version_id',
                $this->request->get('page_version_id'))->where('container',
                $this->request->get('container'))->count() + 1;
        $this->record->component()->save($pageComponent);
    }


    public function afterUpdate()
    {
        // Delete file associations
        if (isset($this->record->file_associations)) {
            foreach ($this->record->file_associations()->get() as $fileAssociation) {
                if ($this->request->get($fileAssociation->identifier) != '' || $this->request->get($fileAssociation->identifier) == 'deleted') {
                    $fileAssociation->delete();
                }
            }
        }
    }


    protected function addFileAssociation($field)
    {
        if ($this->request->get($field) == '' || $this->request->get($field) == 'deleted') {
            return;
        }

        $file = json_decode($this->request->get($field));

        // Create file association
        $fa             = new FileAssociation();
        $fa->file_id    = $file->id;
        $fa->model_type = get_class($this->record);
        $fa->model_id   = $this->record->id;
        $fa->identifier = $field;
        $fa->save();
    }
}