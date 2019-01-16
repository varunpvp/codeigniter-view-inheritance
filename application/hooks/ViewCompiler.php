<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Author: Varun Pujari <varunpvp@gmail.com>
 * Date:   Wed Jan 16 10:44:53 2019 
 */
class ViewCompiler
{
	
	private $CI;

	public function __construct() {
		$this->CI = & get_instance();
	}

	public function render() {
		$view = $this->getView();
		$layout = $this->loadLayoutView($view);

		if ($layout === false) {
			echo $view;
			return;
		}

		echo $this->compileView($view, $layout);
	}
    
	public function compileView($view, $layout) {
		$compiledView = $this->injectSections($view, $layout);
		return $compiledView;
	}

	private function injectSections($view, $layout) {

        $compiledView = $layout;
        $sections = $this->findSections($layout);

        if (! count($sections)) {
            return $view;
        }

        foreach ($sections as $section) {
            $sectionContent = $this->extractSection($view, $section['name']);
            $compiledView = $this->injectSection($compiledView, $section['tag'], $sectionContent);
        }
        return $compiledView;
	}

	private function findSections($layout) {
        $matches = [];
        $sections = [];
        if (preg_match_all("/@provide\(([a-z]+)\)/", $layout, $matches)) {
            $tags = $matches[0];
            foreach ($tags as $index => $tag) {
                $sections[] = ['tag' => $tag, 'name' => $matches[1][$index]];
            }
        }
        return $sections;
    }

    private function extractSection($view, $section) {
        $match = [];
        $found = preg_match("/@section\($section\)(.*?)@endsection/s", $view, $match);
        return $found ? $match[1] : "";
    }

    private function injectSection($layout, $tag, $content) {
        return str_replace($tag, $content, $layout);
    }

	private function loadLayoutView($view) {

		$extends = [];
		$layoutView = NULL;

		if (preg_match("/@extends\(.*\)/", $view, $extends)) {
			$layoutView = substr($extends[0], 9, -1);
		} else if ($this->getControllerLayoutFile() !== false) {
			$layoutView = $this->getControllerLayoutFile();
		}
		
		if ($layoutView) {
			$layoutContent = $this->loadView($layoutView);
			if ($layoutContent !== false) {
				return $layoutContent;
			}
		}

		return false;
	}

	private function getView() {
		return $this->CI->output->get_output();
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
