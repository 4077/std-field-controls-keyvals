<?php namespace std\fieldControls\keyvals\controllers;

// todo ws

class Main extends \Controller
{
    /**
     * @var \ewma\Data\Cell
     */
    private $cell;

    private $indexes;

    public function __create()
    {
        $this->cell = $this->unpackCell();

        $this->instance_($this->cell->xpack());

        $this->dmap('|', 'indexes, editor');

        $this->indexes = $this->data('indexes');
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $v->assign('CONTENT', $this->getContent());

        $this->c('\std\ui button:bind', [
            'selector'                    => $this->_selector('|'),
            'path'                        => '>xhr:openDialog',
            'data'                        => [
                'cell' => $this->cell->xpack()
            ],
            'eventTriggerClosestSelector' => '.cell'
        ]);

        $this->css();

        $this->c('\std\ui\dialogs~:addContainer:' . $this->_nodeId());

        $this->se($this->cell->underscore())->rebind(':reload');

        return $v;
    }

    private function getContent()
    {
        $keyvals = (array)_j($this->cell->value());

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
