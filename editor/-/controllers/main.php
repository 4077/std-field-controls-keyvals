<?php namespace std\fieldControls\keyvals\editor\controllers;

class Main extends \Controller
{
    /**
     * @var \ewma\Data\Cell
     */
    private $cell;

    private $indexes;

    public function __create()
    {
        $cell = $this->cell = $this->unpackCell();

        $this->instance_($cell->xpack());

        $this->dmap('<~|' . $cell->xpack(), 'indexes, editor');

        $this->indexes = $this->data('indexes');
    }

    public function reload()
    {
        $this->jquery('|')->replace($this->view());
    }

    public function view()
    {
        $v = $this->v('|');

        $cell = $this->cell;
        $cellXPack = $cell->xpack();

        $keyvals = (array)_j($cell->value());

        $keyIndex = $this->indexes['key'] ?? 'key';
        $valIndex = $this->indexes['val'] ?? 'val';

        foreach ($keyvals as $n => $keyval) {
            $v->assign('keyval', [
                'NUMBER'        => $n,
                'LABEL_TXT'     => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:labelUpdate',
                    'data'              => [
                        'cell'   => $cellXPack,
                        'number' => $n
                    ],
                    'class'             => 'txt',
                    'fitInputToClosest' => '.label',
                    'content'           => $keyval[$keyIndex] ?? ''
                ]),
                'VALUE_TXT'     => $this->c('\std\ui txt:view', [
                    'path'              => '>xhr:valueUpdate',
                    'data'              => [
                        'cell'   => $cellXPack,
                        'number' => $n
                    ],
                    'class'             => 'txt',
                    'fitInputToClosest' => '.value',
                    'content'           => $keyval[$valIndex]
                ]),
                'DELETE_BUTTON' => $this->c('\std\ui button:view', [
                    'path'    => '>xhr:delete',
                    'data'    => [
                        'cell'   => $cellXPack,
                        'number' => $n
                    ],
                    'class'   => 'delete_button',
                    'title'   => 'Удалить',
                    'content' => '<div class="icon"></div>'
                ])
            ]);
        }

        $v->assign([
                       'ADD_BUTTON' => $this->c('\std\ui button:view', [
                           'path'    => '>xhr:add',
                           'data'    => [
                               'cell' => $cellXPack,
                           ],
                           'class'   => 'add_button green',
                           'content' => 'Добавить'
                       ])
                   ]);

        $this->c('\std\ui\dialogs~:addContainer:' . $this->_nodeId());

        $this->c('\std\ui sortable:bind', [
            'selector'       => $this->_selector('. .keyvals'),
            'items_id_attr'  => 'keyval_number',
            'path'           => '>xhr:arrange',
            'data'           => [
                'cell' => $cellXPack
            ],
            'plugin_options' => [
                'distance' => 15
            ]
        ]);

        $this->css(':\css\std~');

        $this->se($this->cell->underscore())->rebind(':reload');

        return $v;
    }
}
