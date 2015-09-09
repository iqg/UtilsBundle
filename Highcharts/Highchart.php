<?php

namespace DWD\UtilsBundle\Highcharts;
use Ob\HighchartsBundle\Highcharts\Highchart as ObHighchart;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class Highchart extends ObHighchart
{
    protected $container;

    public function __construct( Container $container )
    {
        $this->container = $container;
        parent::__construct();

        $options = $container->getParameter('highcharts');

        $chartOptions = $options['highchart']['option']['chart'];

        if( count( $chartOptions ) > 0 ) {
            foreach ($chartOptions as $option) {
                $this->initChartOption($option);
            }
        }

        $arrayOptions = $options['highchart']['option']['array'];

        if( count( $arrayOptions ) > 0 ) {
            foreach ($arrayOptions as $option) {
                $this->initArrayOption($option);
            }
        }
    }

    public function render( $engine = 'jquery' )
    {
        $chartJS = "";

        // jQuery or MooTools
        if ($engine == 'mootools') {
            $chartJS = 'window.addEvent(\'domready\', function () {';
        } elseif ($engine == 'jquery') {
            $chartJS = "$(function () {";
        }
        $chartJS .= "\n    var " . (isset($this->chart->renderTo) ? $this->chart->renderTo : 'chart') . " = new Highcharts.Chart({\n";

        // Chart Option
        $chartJS .= $this->renderWithJavascriptCallback($this->chart->chart, "chart");

        // Colors
        if (!empty($this->colors)) {
            $chartJS .= "        colors: " . json_encode($this->colors) . ",\n";
        }

        // Credits
        if (get_object_vars($this->credits->credits)) {
            $chartJS .= "        credits: " . json_encode($this->credits->credits) . ",\n";
        }

        // Exporting
        $chartJS .= $this->renderWithJavascriptCallback($this->exporting->exporting, "exporting");

        // Global
        if (get_object_vars($this->global->global)) {
            $chartJS .= "        global: " . json_encode($this->global->global) . ",\n";
        }

        // Labels
        // Lang

        // Legend
        $chartJS .= $this->renderWithJavascriptCallback($this->legend->legend, "legend");

        // Loading
        // Navigation
        // Pane
        if (get_object_vars($this->pane->pane)) {
            $chartJS .= "        pane: " . json_encode($this->pane->pane) . ",\n";
        }

        // PlotOptions
        $chartJS .= $this->renderWithJavascriptCallback($this->plotOptions->plotOptions, "plotOptions");

        // Series
        $chartJS .= $this->renderWithJavascriptCallback($this->series, "series");

        // Subtitle
        if (get_object_vars($this->subtitle->subtitle)) {
            $chartJS .= "        subtitle: " . json_encode($this->subtitle->subtitle) . ",\n";
        }

        // Symbols

        // Title
        if (get_object_vars($this->title->title)) {
            $chartJS .= "        title: " . json_encode($this->title->title) . ",\n";
        }

        // Tooltip
        $chartJS .= $this->renderWithJavascriptCallback($this->tooltip->tooltip, "tooltip");

        // xAxis
        if (gettype($this->xAxis) === 'array') {
            $chartJS .= $this->renderWithJavascriptCallback($this->xAxis, "xAxis");
        } elseif (gettype($this->xAxis) === 'object') {
            $chartJS .= $this->renderWithJavascriptCallback($this->xAxis->xAxis, "xAxis");
        }

        // yAxis
        if (gettype($this->yAxis) === 'array') {
            $chartJS .= $this->renderWithJavascriptCallback($this->yAxis, "yAxis");
        } elseif (gettype($this->yAxis) === 'object') {
            $chartJS .= $this->renderWithJavascriptCallback($this->yAxis->yAxis, "yAxis");
        }

        // custom extend options
        $options = $this->container->getParameter('highcharts');
        $chartOptions = $options['highchart']['option']['chart'];
        $arrayOptions = $options['highchart']['option']['array'];

        if( count($chartOptions) > 0 ) {
            foreach( $chartOptions as $option ) {
                if (get_object_vars($this->$option->$option)) {
                    //$chartJS .= "        {$option}: " . json_encode($this->$option->$option) . ",\n";
                    $chartJS .= $this->renderWithJavascriptCallback($this->$option, $option);
                }
            }
        }

        if( count( $arrayOptions ) > 0 ) {
            foreach( $arrayOptions as $option ) {
                if (gettype($this->$option) === 'array') {
                    $chartJS .= $this->renderWithJavascriptCallback($this->$option, $option);
                } elseif (gettype($this->$option) === 'object') {
                    $chartJS .= $this->renderWithJavascriptCallback($this->$option->$option, $option);
                }
            }
        }

        // trim last trailing comma and close parenthesis
        $chartJS = rtrim($chartJS, ",\n") . "\n    });\n";

        if ($engine != false) {
            $chartJS .= "});\n";
        }

        return trim($chartJS);
    }
}
