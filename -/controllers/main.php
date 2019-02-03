<?php namespace std\fieldControls\keyvals\controllers;

class Main extends \Controller
{
    private $model;

    private $field;

    private $indexes;

    public function __create()
    {
        $model = $this->model = $this->data['model'];
        $field = $this->field = $this->data['field'];

        $this->instance_(underscore_cell($model, $field));

        $this->dmap('|' . underscore_model_type($model), 'data');

        $this->indexes = $this->data('data/indexes');
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $model = $this->model;
        $field = $this->field;

        $v->assign('CONTENT', $this->getContent($model, $field));

        $this->c('\std\ui button:bind', [
            'selector'                    => $this->_selector('|'),
            'path'                        => '>xhr:openDialog',
            'data'                        => [
                'cell' => xpack_cell($model, $field)
            ],
            'eventTriggerClosestSelector' => '.cell'
        ]);

        $this->css();

        $this->c('\std\ui\dialogs~:addContainer:' . $this->_nodeId());

        $this->se(underscore_field($model, $field))->rebind(':reload');

        return $v;
    }

    private function getContent($model, $field)
    {
        $keyvals = (array)_j($model->{$field});

        $keyIndex = $this->indexes['key'] ?? 'key';
        $valIndex = $this->indexes['val'] ?? 'val';

        $list = [];

        foreach ($keyvals as $keyval) {
            if ($keyval[$keyIndex] ?? false) {
                $list[] = $keyval[$keyIndex] . ': ' . $keyval[$valIndex];
            } else {
                $list[] = $keyval[$valIndex];
            }
        }

        return implode('; ', $list) ?: '...';
    }
}
