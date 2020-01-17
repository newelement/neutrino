<?php
namespace Newelement\Neutrino\Tests;
use Illuminate\Support\Facades\Auth;

class LoginTest extends TestCase
{
    public function testSuccessfulLoginWithDefaultCredentials()
    {
        $this->visit(route('shoppe.login'))
             ->type('admin@admin.com', 'email')
             ->type('password', 'password')
             ->press(__('shoppe::generic.login'))
             ->seePageIs(route('shoppe.dashboard'));
    }
    public function testShowAnErrorMessageWhenITryToLoginWithWrongCredentials()
    {
        session()->setPreviousUrl(route('shoppe.login'));
        $this->visit(route('shoppe.login'))
             ->type('john@Doe.com', 'email')
             ->type('pass', 'password')
             ->press(__('shoppe::generic.login'))
             ->seePageIs(route('shoppe.login'))
             ->see(__('auth.failed'))
             ->seeInField('email', 'john@Doe.com');
    }
    public function testRedirectIfLoggedIn()
    {
        Auth::loginUsingId(1);
        $this->visit(route('shoppe.login'))
             ->seePageIs(route('shoppe.dashboard'));
    }
    public function testRedirectIfNotLoggedIn()
    {
        $this->visit(route('shoppe.profile'))
             ->seePageIs(route('shoppe.login'));
    }
    public function testCanLogout()
    {
        Auth::loginUsingId(1);
        $this->visit(route('shoppe.dashboard'))
             ->press(__('shoppe::generic.logout'))
             ->seePageIs(route('shoppe.login'));
    }
    public function testGetsLockedOutAfterFiveAttempts()
    {
        session()->setPreviousUrl(route('shoppe.login'));
        for ($i = 0; $i <= 5; $i++) {
            $t = $this->visit(route('shoppe.login'))
                 ->type('john@Doe.com', 'email')
                 ->type('pass', 'password')
                 ->press(__('shoppe::generic.login'));
        }
        $t->see(__('auth.throttle', ['seconds' => 60]));
    }
}
