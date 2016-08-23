<?php
/**
 * Created by PhpStorm.
 * User: micka
 * Date: 21.07.16
 * Time: 20:20
 */

namespace Project\BackBundle\Datatables;

use Sg\DatatablesBundle\Datatable\Column\ColumnBuilder;
use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class AbstractDatatable
 * @package Project\BackBundle\Datatables
 */
abstract class AbstractDatatable extends AbstractDatatableView
{
    const DISPLAY = 'display';
    const INDEX = 'index';
    const EDIT = 'edit';
    const CREATE = 'create';
    const PROTO = 'project_back_%s_%s';

    /**
     * Builds the datatable.
     *
     * @param array $options
     */
    public function buildDatatable(array $options = [])
    {
        $this->topActions->set(
            [
                'start_html' => '<div class="row"><div class="col-sm-3">',
                'end_html'   => '<hr></div></div>',
                'actions'    => [
                    [
                        'route'      => $this->router->generate($this->makeRoute(self::CREATE)),
                        'label'      => $this->translator->trans('datatables.actions.new'),
                        'icon'       => 'glyphicon glyphicon-plus',
                        'attributes' => [
                            'rel'   => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.new'),
                            'class' => 'btn btn-primary',
                            'role'  => 'button',
                        ],
                    ],
                ],
            ]
        );

        $this->features->set(
            [
                'auto_width'    => true,
                'defer_render'  => false,
                'info'          => true,
                'jquery_ui'     => false,
                'length_change' => true,
                'ordering'      => true,
                'paging'        => true,
                'processing'    => true,
                'scroll_x'      => false,
                'scroll_y'      => '',
                'searching'     => true,
                'state_save'    => false,
                'delay'         => 0,
                'extensions'    => [],
            ]
        );

        $this->ajax->set(
            [
                'url'  => $this->router->generate($this->makeRoute(self::INDEX)),
                'type' => 'GET',
            ]
        );

        $this->options->set(
            [
                'display_start'                 => 0,
                'defer_loading'                 => -1,
                'dom'                           => 'lfrtip',
                'length_menu'                   => [50, 100, 150, 200],
                'order_classes'                 => true,
                'order'                         => [[0, 'desc']],
                'order_multi'                   => true,
                'page_length'                   => 50,
                'paging_type'                   => Style::FULL_NUMBERS_PAGINATION,
                'renderer'                      => '',
                'scroll_collapse'               => false,
                'search_delay'                  => 0,
                'state_duration'                => 7200,
                'stripe_classes'                => [],
                'class'                         => Style::BOOTSTRAP_3_STYLE,
                'individual_filtering'          => true,
                'individual_filtering_position' => 'head',
                'use_integration_options'       => true,
                'force_dom'                     => false,
            ]
        );

        $this->config($this->columnBuilder);
        $this->buttonCrud();
    }

    private function makeRoute(string $action)
    {
        return sprintf(self::PROTO, $this->getClassname(), $action);
    }

    abstract protected function config(ColumnBuilder $builder);

    protected function buttonCrud()
    {
        $this->columnBuilder->add(
            null,
            'action',
            [
                'title'   => $this->translator->trans('datatables.actions.title'),
                'actions' => [
                    [
                        'route'            => $this->makeRoute(self::DISPLAY),
                        'route_parameters' => [
                            'id' => 'id',
                        ],
                        'label'            => $this->translator->trans('datatables.actions.show'),
                        'icon'             => 'glyphicon glyphicon-eye-open',
                        'attributes'       => [
                            'rel'   => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.show'),
                            'class' => 'btn btn-primary btn-xs',
                            'role'  => 'button',
                        ],
                    ],
                    [
                        'route'            => $this->makeRoute(self::EDIT),
                        'route_parameters' => [
                            'id' => 'id',
                        ],
                        'label'            => $this->translator->trans('datatables.actions.edit'),
                        'icon'             => 'glyphicon glyphicon-edit',
                        'attributes'       => [
                            'rel'   => 'tooltip',
                            'title' => $this->translator->trans('datatables.actions.edit'),
                            'class' => 'btn btn-primary btn-xs',
                            'role'  => 'button',
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Returns Entity.
     *
     * @return string
     */
    public function getEntity()
    {
        return null;
    }

    /**
     * Returns the name of this datatable view.
     *
     * @return string
     */
    public function getName()
    {
        return null;
    }
}
