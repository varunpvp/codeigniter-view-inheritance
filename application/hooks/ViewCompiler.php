<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Author: Varun Pujari <varunpvp@gmail.com>
 * Date:   Wed Jan 16 10:44:53 2019
 */
class ViewCompiler
{

    private $CI;

    private $view;
    private $layout;
    private $compiledView;

    public function __construct() {
        $this->CI = & get_instance();
    }

    public function compile() {
        $this->view = $this->getView();
        $this->loadLayoutView();

        if ($this->layout) {
            $this->compileView();
        }

        $this->renderView();
    }

    public function renderView() {
        $this->setView($this->compiledView ? $this->compiledView : $this->view);
    }

    public function compileView() {
        $this->injectSections();
    }

    private function injectSections() {

        $sections = $this->findSections();

        if (! count($sections)) {
            return;
        }

        $this->compiledView = $this->layout;

        foreach ($sections as $section) {
            $this->injectSection($section);
        }
    }

    private function findSections() {
        $matches = [];
        $sections = [];
        if (preg_match_all("/@provide\(([a-z]+)\)/", $this->layout, $matches)) {
            $tags = $matches[0];
            foreach ($tags as $index => $tag) {
                $sections[] = ['tag' => $tag, 'name' => $matches[1][$index]];
            }
        }
        return $sections;
    }

    private function injectSection($section) {
        $content = $this->extractSection($section['name']);
        $this->compiledView = str_replace($section['tag'], $content, $this->compiledView);
    }

    private function extractSection($section) {
        $match = [];
        $found = preg_match("/@section\($section\)(.*?)@endsection/s", $this->view, $match);
        return $found ? $match[1] : "";
    }

    private function loadLayoutView() {

        $layoutView = $this->getLayoutView();

        if ($layoutView) {
            $this->layout = $this->loadView($layoutView);
        }
	}

    private function getLayoutView() {
        $extends = [];
        $layoutView = NULL;

        if (preg_match("/@extends\(.*\)/", $this->view, $extends)) {
            $layoutView = substr($extends[0], 9, -1);
        } else if ($this->getControllerLayoutFile() !== false) {
            $layoutView = $this->getControllerLayoutFile();
        }
        return $layoutView;
    }

    private function getView() {
        return $this->CI->output->get_output();
    }
    
    private function setView($view) {
        return $this->CI->output->set_output($view);
    }

    private function loadView($view) {
        $viewPath = $this->getViewPath($view);
        return file_exists($viewPath) ? $this->CI->load->file($viewPath, true) : false;
    }

    private function getViewPath($view) {
        return BASEPATH . "../application/views/$view.php";
    }

    private function getControllerLayoutFile() {
        return (! empty($this->CI->layout)) ? $this->CI->layout : false;
    }
}
