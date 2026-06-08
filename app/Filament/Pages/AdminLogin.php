<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login;
use Filament\Schemas\Schema;

class AdminLogin extends Login
{
	public function form(Schema $schema): Schema
	{
		return $schema
			->components([
				$this->getEmailFormComponent(),
				$this->getPasswordFormComponent(),
				// $this->getRememberFormComponent(),
			]);
	}
}
