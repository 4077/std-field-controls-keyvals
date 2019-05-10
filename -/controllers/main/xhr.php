<?php namespace std\fieldControls\keyvals\controllers\main;

class Xhr extends \Controller
{
    public $allow = self::XHR;

    public function openDialog()
    {
        if ($cell = $this->unxpackCell()) {
            $this->dmap('~|' . $cell->xpack(), 'editor');

            $titleCall = $this->data('editor/calls/title');

            if ($titleCall) {
                $titleCall = \ewma\Data\Data::tokenize($titleCall, [
                    '%model' => $cell->model
                ]);
            }

            $this->c('\std\ui\dialogs~:open:props|' . $this->_nodeId('<'), [
                'path'          => 'editor~:view',
                'data'          => [
                    'cell' => $cell->pack()
                ],
                'pluginOptions' => [
                    'title' => $titleCall ? $this->_call($titleCall)->perform() : ''
                ],
                'default'       => [
                    'pluginOptions' => [
                        'width'  => 600,
                        'height' => 200
                    ]
                ]
            ]);
        }

        // todo close on delete
//            $this->e(underscore_model($model))->trigger([
//                                                            'model'    => $model,
//                                                            'data_set' => _j64($this->data('data_set'))
//                                                        ]);
    }
}
