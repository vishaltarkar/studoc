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
    protected const BACK_SLUG = 'back';
    protected const BACK_SHORT_SLUG = 'b';
    protected const BACK_LBL = 'Go back';

    // Quit choice Values
    protected const QUIT_SLUG = 'quit';
    protected const QUIT_SHORT_SLUG = 'q';
    protected const QUIT_LBL = 'Quit';

    // General Choices
    protected const BASE_CHOICES = [
        self::BACK_SLUG => self::BACK_LBL,
        self::QUIT_SLUG => self::QUIT_LBL,
    ];

    // generic quite text
    protected const QUIT_TXT = "Are you sure want to quite?";

    // generic good bye text
    protected const GOODBYE_TXT = "Bye! Thank you!";


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
    protected function allChoices()
    {
        return array_merge($this->menuChoices(), $this->baseChoices());
    }


    private function baseChoices()
    {
        return self::BASE_CHOICES;
    }

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

    protected function handleAllChoice(string $choice)
    {
        $this->handleBaseChoices($choice);
        $this->handleMenuChoices($choice);
    }

    /**
     */
    private function confirmQuit()
    {
        if ($this->confirm(self::QUIT_TXT)) {
            $this->info(self::GOODBYE_TXT);
        } else {
            $this->backCommand();
        }
    }
}
