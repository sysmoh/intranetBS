<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Form\Form;


class Color{
    const NONE = '';
    const BLUE = 'blue';
    const WHITE = 'white';
    const YELLOW = 'yellow';
    const BLACK = 'black';
    const GREEN = 'green';
}

class Format{
    const NONE = '';
    const BOLD = 'bold';
    const UNDERSCORE = 'underscore';
}

class Mode{
    const STANDARD = '';
    const ERROR = 'error';
    const INFO = 'info';
    const COMMENT = 'comment';
    const SUCCESS = 'success';
}

/**
 * Cette class est déstinée à simplifier les output de nos commandes.
 *
 *
 * doc: http://symfony.com/doc/current/components/console/introduction.html
 *
 * Avaliable colors: black, red, green, yellow, blue, magenta, cyan, white.
 *
 * Avaliable formats: bold, underscore, blink, reverse.
 *
 *
 * verbosity: VERBOSITY_DEBUG > VERBOSITY_VERY_VERBOSE > VERBOSITY_VERBOSE > VERBOSITY_NORMAL
 *
 * pour infos aller lire :
 * @link http://symfony.com/blog/new-in-symfony-2-8-console-style-guide
 *
 *
 * Class ConsoleOutput
 * @package AppBundle\Command
 */
class ConsoleOutput
{
    /** @var OutputInterface  */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    public function write($string = null)
    {
        if(is_null($string))
            $string = ' ';
        $this->output->write($string);
        return $this;
    }

    public function writeln($string = null)
    {
        if(is_null($string))
            $string = ' ';
        $this->output->writeln($string);
        return $this;
    }

    public function writeMode($string = null,$mode = Mode::STANDARD){


            switch($mode){
                case Mode::INFO:
                case Mode::STANDARD:
                    //do nothing
                    break;
                case Mode::ERROR:
                    $string = '<error>'.$string.'</error> ';
                    break;
                case Mode::SUCCESS:
                    $string = '<info>'.$string.'</info> ';
                    break;
                case Mode::COMMENT:
                    $string = '<comment>'.$string.'</comment> ';
                    break;
            }
            $this->output->write($string);
        return $this;

    }

    public function writeCustom($string,$colorBackground = Color::NONE,$colorFont = Color::NONE,$format = Format::NONE)
    {
        $this->output->write('<fg='.$colorFont.';bg='.$colorBackground.';options='.$format.'>'.$string.'</>');
        return $this;
    }

    public function blueLabel($string)
    {
        $this->writeCustom($string,Color::BLUE,Color::WHITE);
        return $this;
    }

    public function yellowLabel($string)
    {
        $this->writeCustom($string,Color::YELLOW,Color::BLACK);
        return $this;
    }

    public function greenLabel($string)
    {
        $this->writeCustom($string,Color::GREEN,Color::WHITE);
        return $this;
    }

    public function error($string){
        $this->writeMode($string,Mode::ERROR);
        return $this;
    }

    public function info($string){
        $this->writeMode($string,Mode::INFO);
        return $this;
    }

    public function success($string){
        $this->writeMode($string,Mode::SUCCESS);
        return $this;
    }

    public function comment($string){
        $this->writeMode($string,Mode::COMMENT);
        return $this;
    }



}