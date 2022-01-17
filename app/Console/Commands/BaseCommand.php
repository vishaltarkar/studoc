<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BaseCommand extends Command
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
    protected const QUIT_SLUG = 'quite';
    protected const QUIT_SHORT_SLUG = 'q';
    protected const QUIT_LBL = 'Quit';

    // General Choices
    protected const BASE_CHOICES = [
        self::BACK_SLUG => self::BACK_LBL,
        self::QUIT_SLUG => self::QUIT_LBL,
    ];

    // generic quite text
    protected const QUITE_TXT = "Are you sure want to quite?";

    // generic good bye text
    protected const GOODBYE_TXT = "Bye! Thank you!";


    public function handle()
    {
        $choice = $this->choice(vsprintf($this->menuTitle(), $this->allOptions());
        // $this->handleBaseOptions($option);
        // $this->handleMenuOptions($option);
    }

}
