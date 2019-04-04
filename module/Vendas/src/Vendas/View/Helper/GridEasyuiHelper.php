<?php
namespace Vendas\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Renderer\PhpRenderer;
use Zend\View\Resolver\AggregateResolver;
use Zend\View\Resolver\TemplateMapResolver;

/**
 * Classe para renderização de grids da biblioteca easyui
 *
 * @author Ederson Silva
 */
class GridEasyuiHelper extends AbstractHelper {
    /**
     * Caminho para template
     * @var string
     */
    private $path_template_default;
    
    private $default_grid_props = [
        'fitColumns' => true,
        'singleSelect' => true,
        'method' => 'GET',
        'collapsible' => false,
        'fitColumns' => true,
        'autoRowHeight' => true,
        'pagination' => true,
        'remoteFilter' => true,
        'remoteSort' => true,
        'nowrap' => false,
        //O valor de pageSize deve constar em pageList
        'pageSize' => 50,
        'pageList' => [10,50,50,100,150,200],
    ];

    public function __invoke(array $grid_props, $view = null, $path_template = null) {
        
        $grid = array_merge($this->default_grid_props, $grid_props);

        //Setando o template
        if(is_null($path_template)){
            $this->path_template_default = realpath(getcwd()).'/module/Vendas/view/easyuigrid/grid-default.phtml';
        } else {
            $this->path_template_default = $path_template;
        }

        $renderer = new PhpRenderer();
        $resolver = new AggregateResolver();

        $map = new TemplateMapResolver([
            'template' => $this->path_template_default
        ]);
        $renderer->setResolver($map);

        $resolver->attach($map);

        echo $renderer->render('template', ["grid" => $grid, "view" => $view]);

    }
}