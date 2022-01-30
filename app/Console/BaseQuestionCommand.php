<?php

namespace App\Console;

use Illuminate\Console\Command;

abstract class BaseQuestionCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    // Back choice values
    public const BACK_SLUG = 'back';
    public const BACK_SHORT_SLUG = 'b';
    public const BACK_LBL = 'Go back';

    // Quit choice Values
    public const QUIT_SLUG = 'quit';
    public const QUIT_SHORT_SLUG = 'q';
    public const QUIT_LBL = 'Quit';

    // General Choices
    protected const BASE_CHOICES = [
        self::BACK_SLUG => self::BACK_LBL,
        self::QUIT_SLUG => self::QUIT_LBL,
    ];

    // generic quite text
    public const QUIT_TXT = "Are you sure want to quite?";

    // generic good bye text
    public const GOODBYE_TXT = "Bye! Thank you!";

    public function handle()
    {
        $choice = $this->choice($this->menuTitle(), $this->allChoices());
        $this->handleAllChoice($choice);
    }

    /**
     */
    abstract protected function backCommand();

    abstract protected function handleMenuChoices(string $choice);

    /**
     * a function to merge and return base & main choices
     *
     * @return array
     */
    protected function allChoices() :array
    {
        return array_merge($this->menuChoices(), $this->baseChoices());
    }

    /**
     * a function to get base choice for app
     *
     * @return array
     */
    private function baseChoices() :array
    {
        return self::BASE_CHOICES;
    }

    /**
     * Handle base choice of application
     *
     * @param string|null $option
     * @return void
     */
    protected function handleBaseChoices(string $option = null)
    {
        switch ($option) {
            case self::BACK_SLUG:
            case self::BACK_SHORT_SLUG:
                $this->backCommand();
                break;
            case self::QUIT_SLUG:
            case self::QUIT_SHORT_SLUG:
                $this->confirmQuit();
                break;
        }
    }

    /**
     * handle all choices of base as well as from other menu
     *
     * @param string $choice
     * @return void
     */
    protected function handleAllChoice(string $choice)
    {
        $this->handleBaseChoices($choice);
        $this->handleMenuChoices($choice);
    }

    /**
     * Prompt console to ask user to exit or not
     */
    private function confirmQuit()
    {
        if ($this->confirm(self::QUIT_TXT)) {
            $this->info(self::GOODBYE_TXT);
            return 0;
        } else {
            $this->backCommand();
        }
    }

    /**
     * Handle Prompt Input from the User
     *
     * @param string $title
     * @param string $label
     * @param integer $limit
     * @return string
     */
    protected function promptInputWithValidation(string $title, string $label, $limit = 255) :string
    {
        // ask user for the questions
        $val = trim($this->ask($title));
        if ($val == '') {
            $this->error('Error : '.$label.' can`t be blank.');
            $val = $this->promptInputWithValidation($title, $label);
        } else if (\Illuminate\Support\Str::length($val) > $limit) {
            $this->error('Error : '.$label.' length should be less than '.$limit.' characters.');
            $val = $this->promptInputWithValidation($title, $label);
        }
        return $val;
    }
}
