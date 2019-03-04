<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Author: Varun Pujari <varunpvp@gmail.com>
 * Date:   Wed Jan 16 10:44:53 2019
 */
class ViewCompiler extends AbstractViewCompiler
{

    private $CI;

    public function __construct() {
        $this->CI = & get_instance();
    }

    protected function getView() {
        return $this->CI->output->get_output();
    }

    protected function setView($view) {
        echo $view;
    }

    protected function loadView($view) {
        $viewPath = $this->getViewPath($view);
        return file_exists($viewPath) ? $this->CI->load->file($viewPath, true) : false;
    }

    protected function getControllerLayoutFile() {
        return (! empty($this->CI->layout)) ? $this->CI->layout : false;
    }

    protected function getViewPath($view) {
        return BASEPATH . "../application/views/$view.php";
    }
}

abstract class AbstractViewCompiler {

    private const REGEX_EXTENDS = "/@extends\(.*\)/";
    private const REGEX_PROVIDE = "/@provide\(([a-z]+)\)/";
    private const REGEX_SECTION = "/@section\(%s\)(.*?)@endsection/s";

    private $view;
    private $layout;
    private $compiledView;

    abstract protected function setView($view);

    abstract protected function getView();

    abstract protected function getControllerLayoutFile();

    abstract protected function loadView($view);

    public function compile() {
        $this->view = $this->getView();
        $this->loadLayoutView();

        if ($this->layout) {
            $this->compileView();
        }

        $this->renderView();
    }

    private function renderView() {
        $this->setView($this->compiledView ? $this->compiledView : $this->view);
    }

    private function compileView() {
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
        if (preg_match_all(self::REGEX_PROVIDE, $this->layout, $matches)) {
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
        $found = preg_match(sprintf(self::REGEX_SECTION, $section), $this->view, $match);
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

        if (preg_match(self::REGEX_EXTENDS, $this->view, $extends)) {
            $layoutView = substr($extends[0], 9, -1);
        } else if ($this->getControllerLayoutFile() !== false) {
            $layoutView = $this->getControllerLayoutFile();
        }
        return $layoutView;
    }

}
