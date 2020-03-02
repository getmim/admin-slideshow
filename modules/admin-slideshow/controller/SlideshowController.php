<?php
/**
 * SlideshowController
 * @package admin-slideshow
 * @version 0.0.1
 */

namespace AdminSlideshow\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibPagination\Library\Paginator;
use Slideshow\Model\Slideshow;

class SlideshowController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['component', 'slideshow']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_slideshow)
            return $this->show404();

        $slides = (object)[
            'images' => ''
        ];

        $id = $this->req->param->id;
        if($id){
            $slides = Slideshow::getOne(['id'=>$id]);
            if(!$slides)
                return $this->show404();
            $params = $this->getParams('Edit Slideshow');
        }else{
            $params = $this->getParams('Create New Slideshow');
        }

        $form           = new Form('admin.component-slideshow.edit');
        $params['form'] = $form;

        if(!($valid = $form->validate($slides)) || !$form->csrfTest('noob'))
            return $this->resp('slideshow/edit', $params);

        if($id){
            if(!Slideshow::set((array)$valid, ['id'=>$id]))
                deb(Slideshow::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!Slideshow::create((array)$valid))
                deb(Slideshow::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'slideshow',
            'original' => $slides,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminSlideshow');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_slideshow)
            return $this->show404();

        $pcond = $cond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        list($page, $rpp) = $this->req->getPager(20, 50);

        $slides = Slideshow::get($cond, $rpp, $page, ['name'=>true]) ?? [];
        if($slides)
            $slides = Formatter::formatMany('slideshow', $slides, ['user']);

        $params           = $this->getParams('Site Slideshow');
        $params['slides'] = $slides;
        $params['form']   = new Form('admin.component-slideshow.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = Slideshow::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminSlideshow'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('slideshow/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_slideshow)
            return $this->show404();

        $id     = $this->req->param->id;
        $slides = Slideshow::getOne(['id'=>$id]);
        $next   = $this->router->to('adminSlideshow');
        $form   = new Form('admin.component-slideshow.index');

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'slideshow',
            'original' => $slides,
            'changes'  => null
        ]);

        Slideshow::remove(['id'=>$id]);
        $this->res->redirect($next);
    }
}