<?php namespace std\fieldControls\keyvals\editor\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    /**
     * @var \ewma\Data\Cell
     */
    private $cell;

    private $indexes;

    public function __create()
    {
        $this->cell = $this->unpackCell();

        $this->dmap('<~|' . underscore_model_type($this->cell->model), 'data');

        $this->indexes = $this->data('data/indexes');
    }

    private function performUpdateCallback()
    {
        if ($updateCallback = $this->data('data/editor/callbacks/update')) {
            $updateCallback = \ewma\Data\Data::tokenize($updateCallback, [
                '%cell' => $this->unxpackCell()
            ]);

            $this->_call($updateCallback)->perform();
        }
    }

    public function add()
    {
        if ($cell = $this->cell) {
            $keyvals = (array)_j($cell->value());

            $keyvals[] = [
                $this->indexes['key'] ?? 'key' => '',
                $this->indexes['val'] ?? 'val' => ''
            ];

            $cell->value(j_($keyvals));

            $this->performUpdateCallback();

            $this->se(underscore_field($cell->model, $cell->field))->trigger([
                                                                                 'model' => $cell->model,
                                                                                 'field' => $cell->field,
                                                                                 'cell'  => $cell
                                                                             ]);
        }
    }

    public function delete()
    {
        if ($cell = $this->cell) {
            if ($this->data('discarded')) {
                $this->c('\std\ui\dialogs~:close:deleteConfirm|' . $this->_nodeId('<'));
            } else {
                $keyvals = (array)_j($cell->value());

                $keyvalNumber = $this->data['number'];

                if ($this->data('confirmed')) {
                    unset($keyvals[$keyvalNumber]);
                    array_values($keyvals);

                    $cell->value(j_($keyvals));

                    $this->performUpdateCallback();

                    $this->se(underscore_field($cell->model, $cell->field))->trigger([
                                                                                         'model' => $cell->model,
                                                                                         'field' => $cell->field,
                                                                                         'cell'  => $cell
                                                                                     ]);

                    $this->c('\std\ui\dialogs~:close:deleteConfirm|' . $this->_nodeId('<'));
                } else {
                    $this->c('\std\ui\dialogs~:open:deleteConfirm|' . $this->_nodeId('<'), [
                        'path' => '\std dialogs/confirm~:view',
                        'data' => [
                            'confirm_call' => $this->_abs(':delete', [
                                'number' => $keyvalNumber,
                                'cell'   => $this->data['cell']
                            ]),
                            'discard_call' => $this->_abs(':delete', [
                                'number' => $keyvalNumber,
                                'cell'   => $this->data['cell']
                            ]),
                            'message'      => 'Удалить <b>' . $keyvals[$keyvalNumber]['label'] . '</b>?'
                        ]
                    ]);
                }
            }
        }
    }

    public function labelUpdate()
    {
        if ($cell = $this->cell) {
            $keyvals = (array)_j($cell->value());

            if (isset($keyvals[$this->data('number')])) {
                $txt = \std\ui\Txt::value($this);

                $keyvals[$this->data('number')][$this->indexes['key'] ?? 'key'] = $txt->value;

                $cell->value(j_($keyvals));

                $txt->response();

                $this->performUpdateCallback();

                $this->se(underscore_field($cell->model, $cell->field))->trigger([
                                                                                     'model' => $cell->model,
                                                                                     'field' => $cell->field,
                                                                                     'cell'  => $cell
                                                                                 ]);
            }
        }
    }

    public function valueUpdate()
    {
        if ($cell = $this->cell) {
            $keyvals = (array)_j($cell->value());

            if (isset($keyvals[$this->data('number')])) {
                $txt = \std\ui\Txt::value($this);

                $keyvals[$this->data('number')][$this->indexes['val'] ?? 'val'] = $txt->value;

                $cell->value(j_($keyvals));

                $txt->response();

                $this->performUpdateCallback();

                $this->se(underscore_field($cell->model, $cell->field))->trigger([
                                                                                     'model' => $cell->model,
                                                                                     'field' => $cell->field,
                                                                                     'cell'  => $cell
                                                                                 ]);
            }
        }
    }

    public function arrange()
    {
        $cell = $this->cell;

        if ($cell && $this->dataHas('sequence')) {
            $keyvals = (array)_j($cell->value());

            $keyvals = map($keyvals, $this->data['sequence']);

            $cell->value(j_($keyvals));

            $this->performUpdateCallback();

            $this->se(underscore_field($cell->model, $cell->field))->trigger([
                                                                                 'model' => $cell->model,
                                                                                 'field' => $cell->field,
                                                                                 'cell'  => $cell
                                                                             ]);
        }
    }
}
