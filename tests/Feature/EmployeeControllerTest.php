<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\Helper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

beforeEach(function () {
    $this->helper = Mockery::mock(Helper::class);
    $this->controller = new EmployeeController;
});

afterEach(function () {
    Mockery::close();
    Carbon::setTestNow(); // Reset Carbon test time
});

it('displays current and future employees correctly', function () {
    // Arrange
    $employee1 = Employee::factory()->create([
        'id' => 1,
        'email' => 'test1@example.com',
        'bu_start' => 'even',
    ]);
    $employee2 = Employee::factory()->create([
        'id' => 2,
        'email' => 'test2@example.com',
        'bu_start' => 'odd',
    ]);
    $employee3 = Employee::factory()->create([
        'id' => 3,
        'email' => 'test3@example.com',
        'bu_start' => '',
    ]);

    $currentMonth = Carbon::now()->isoFormat('YYYY-MM');

    // Mock people for current month
    $mockPeople = collect([
        (object) ['employee_id' => 1, 'name' => 'John Doe', 'staffgroup_id' => 1],
        (object) ['employee_id' => 2, 'name' => 'Jane Smith', 'staffgroup_id' => 2],
    ]);

    $mockPastPeople = collect([]);

    $this->helper->shouldReceive('getPeopleForMonth')
        ->with($currentMonth)
        ->once()
        ->andReturn($mockPeople);

    $this->helper->shouldReceive('getPastPeople')
        ->with($currentMonth)
        ->once()
        ->andReturn($mockPastPeople);

    $this->helper->shouldReceive('staffgroupMayReceiveEMail')
        ->with(1)
        ->once()
        ->andReturn(true);

    $this->helper->shouldReceive('staffgroupMayReceiveEMail')
        ->with(2)
        ->once()
        ->andReturn(false);

    // Act
    $result = $this->controller->index($this->helper);

    // Assert
    expect($result)->toBeInstanceOf(View::class);
    expect($result->name())->toBe('employees.index');

    $viewData = $result->getData();
    expect($viewData)->toHaveKeys(['current', 'future']);
    expect($viewData['current'])->toHaveCount(2);
    expect($viewData['future'])->toHaveCount(1); // Employee 3 should be in future
})->skip();

it('handles email validation warnings correctly', function () {
    // Arrange
    $employee = Employee::factory()->create([
        'id' => 1,
        'email' => 'invalid-email', // No @ symbol
        'bu_start' => 'even',
    ]);

    $currentMonth = Carbon::now()->isoFormat('YYYY-MM');

    $mockPeople = collect([
        (object) ['employee_id' => 1, 'name' => 'John Doe', 'staffgroup_id' => 1],
    ]);

    $this->helper->shouldReceive('getPeopleForMonth')
        ->with($currentMonth)
        ->once()
        ->andReturn($mockPeople);

    $this->helper->shouldReceive('getPastPeople')
        ->with($currentMonth)
        ->once()
        ->andReturn(collect([]));

    $this->helper->shouldReceive('staffgroupMayReceiveEMail')
        ->with(1)
        ->once()
        ->andReturn(true); // Should have email but doesn't have valid one

    // Act
    $result = $this->controller->index($this->helper);

    // Assert
    $viewData = $result->getData();
    $currentEmployee = $viewData['current'][0];
    expect($currentEmployee->warning)->toBeTrue();
});

it('returns edit view with employee and BU data', function () {
    // Arrange
    $employee = Employee::factory()->create(['id' => 1]);

    // Act
    $result = $this->controller->edit(1);

    // Assert
    expect($result)->toBeInstanceOf(View::class);
    expect($result->name())->toBe('employees.edit');

    $viewData = $result->getData();
    expect($viewData)->toHaveKeys(['employee', 'bu']);
    expect($viewData['employee']->id)->toBe(1);
    expect($viewData['bu'])->toBeArray();
});

it('throws exception for non-existent employee', function () {
    // Act & Assert
    expect(fn () => $this->controller->edit(999))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

it('updates employee and redirects with flash message', function () {
    // Arrange
    $employee = Employee::factory()->create([
        'id' => 1,
        'hash' => 'old-hash',
    ]);

    $request = Mockery::mock(UpdateEmployeeRequest::class);
    $request->shouldReceive('all')->once()->andReturn([
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $session = Mockery::mock();
    $session->shouldReceive('flash')
        ->with('info', 'Der Eintrag für die Person wurde geändert.')
        ->once();
    $request->shouldReceive('session')->once()->andReturn($session);

    // Act
    $result = $this->controller->update($request, 1);

    // Assert
    expect($result)->toBeInstanceOf(RedirectResponse::class);

    $employee->refresh();
    expect($employee->hash)->not()->toBe('old-hash');
    expect(strlen($employee->hash))->toBe(16); // Str::random() default length
});

it('throws exception for non-existent employee in update', function () {
    // Arrange
    $request = Mockery::mock(UpdateEmployeeRequest::class);

    // Act & Assert
    expect(fn () => $this->controller->update($request, 999))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

it('displays employees for specific month', function () {
    // Arrange
    $year = 2024;
    $month = 3;
    $formattedMonth = '2024-03';

    $mockEpisodes = collect([
        (object) ['employee_id' => 1, 'name' => 'John Doe'],
    ]);

    $mockChanges = collect([
        (object) ['change_type' => 'start', 'date' => '2024-03-01'],
    ]);

    $this->helper->shouldReceive('validateAndFormatDate')
        ->with($year, $month)
        ->once()
        ->andReturn($formattedMonth);

    $this->helper->shouldReceive('getPeopleForMonth')
        ->with($formattedMonth)
        ->once()
        ->andReturn($mockEpisodes);

    $this->helper->shouldReceive('getChangesForMonth')
        ->with($formattedMonth)
        ->once()
        ->andReturn($mockChanges);

    $this->helper->shouldReceive('getNextMonthUrl')
        ->with('employees/month/', $year, $month)
        ->once()
        ->andReturn('http://example.com/next');

    $this->helper->shouldReceive('getPreviousMonthUrl')
        ->with('employees/month/', $year, $month)
        ->once()
        ->andReturn('http://example.com/previous');

    // Act
    $result = $this->controller->showMonth($this->helper, $year, $month);

    // Assert
    expect($result)->toBeInstanceOf(View::class);
    expect($result->name())->toBe('employees.show_month');

    $viewData = $result->getData();
    expect($viewData)->toHaveKeys([
        'episode_changes', 'episodes', 'readable_month',
        'next_month_url', 'previous_month_url',
    ]);
    expect($viewData['readable_month'])->toBe('März 2024');
});

it('shows current VK for year', function () {
    // Arrange
    $whichVk = 'planned';
    $currentYear = 2024;

    $this->helper->shouldReceive('getPlannedYear')
        ->once()
        ->andReturn($currentYear);

    $this->helper->shouldReceive('sumUpVKForYear')
        ->with($whichVk, $currentYear, Mockery::type('array'), Mockery::type('array'))
        ->once();

    $this->helper->shouldReceive('getNextYearUrl')
        ->with('employees/vk/'.$whichVk.'/', $currentYear)
        ->once()
        ->andReturn('http://example.com/next');

    $this->helper->shouldReceive('getPreviousYearUrl')
        ->with('employees/vk/'.$whichVk.'/', $currentYear)
        ->once()
        ->andReturn('http://example.com/previous');

    // Act
    $result = $this->controller->showCurrentVKForYear($this->helper, $whichVk);

    // Assert
    expect($result)->toBeInstanceOf(View::class);
    expect($result->name())->toBe('employees.show_vk_for_year');
});

it('shows VK for specific year', function () {
    // Arrange
    $whichVk = 'actual';
    $year = 2023;

    $this->helper->shouldReceive('sumUpVKForYear')
        ->with($whichVk, $year, Mockery::type('array'), Mockery::type('array'))
        ->once();

    $this->helper->shouldReceive('getNextYearUrl')
        ->with('employees/vk/'.$whichVk.'/', $year)
        ->once()
        ->andReturn('http://example.com/next');

    $this->helper->shouldReceive('getPreviousYearUrl')
        ->with('employees/vk/'.$whichVk.'/', $year)
        ->once()
        ->andReturn('http://example.com/previous');

    // Act
    $result = $this->controller->showVKForYear($this->helper, $whichVk, $year);

    // Assert
    expect($result)->toBeInstanceOf(View::class);
    expect($result->name())->toBe('employees.show_vk_for_year');

    $viewData = $result->getData();
    expect($viewData['which_vk'])->toBe($whichVk);
    expect($viewData['year'])->toBe($year);
});

it('shows current stellenplan', function () {
    // Arrange
    $currentYear = 2024;

    $this->helper->shouldReceive('getPlannedYear')
        ->once()
        ->andReturn($currentYear);

    $this->helper->shouldReceive('sumUpVKForYearWithoutStaffgroups')
        ->with($currentYear, Mockery::type('array'), Mockery::type('array'))
        ->once();

    $this->helper->shouldReceive('getNextYearUrl')
        ->with('employees/stellenplan/', $currentYear)
        ->once()
        ->andReturn('http://example.com/next');

    $this->helper->shouldReceive('getPreviousYearUrl')
        ->with('employees/stellenplan/', $currentYear)
        ->once()
        ->andReturn('http://example.com/previous');

    // Act
    $result = $this->controller->showCurrentStellenplan($this->helper);

    // Assert
    expect($result)->toBeInstanceOf(View::class);
    expect($result->name())->toBe('employees.show_stellenplan_for_year');
});

it('shows stellenplan for specific year', function () {
    // Arrange
    $year = 2023;

    $this->helper->shouldReceive('sumUpVKForYearWithoutStaffgroups')
        ->with($year, Mockery::type('array'), Mockery::type('array'))
        ->once();

    $this->helper->shouldReceive('getNextYearUrl')
        ->with('employees/stellenplan/', $year)
        ->once()
        ->andReturn('http://example.com/next');

    $this->helper->shouldReceive('getPreviousYearUrl')
        ->with('employees/stellenplan/', $year)
        ->once()
        ->andReturn('http://example.com/previous');

    // Act
    $result = $this->controller->showStellenplan($this->helper, $year);

    // Assert
    expect($result)->toBeInstanceOf(View::class);
    expect($result->name())->toBe('employees.show_stellenplan_for_year');

    $viewData = $result->getData();
    expect($viewData['year'])->toBe($year);
});

it('calculates BU start correctly for odd year', function () {
    // Arrange
    Carbon::setTestNow(Carbon::create(2023, 6, 15)); // Odd year

    // We need to test the private method indirectly through a method that uses it
    // Since _calculateBUStart is private, we test it through edit method
    $employee = Employee::factory()->create();
    $editResult = $this->controller->edit($employee->id);
    $buData = $editResult->getData()['bu'];

    // Assert
    expect($buData)->toHaveKey('');
    expect($buData)->toHaveKey('even');
    expect($buData)->toHaveKey('odd');
    expect($buData['even'])->toContain('2022 - 2023');
    expect($buData['odd'])->toContain('2023 - 2024');
});

it('calculates BU start correctly for even year', function () {
    // Arrange
    Carbon::setTestNow(Carbon::create(2024, 6, 15)); // Even year

    // Act
    $employee = Employee::factory()->create();
    $editResult = $this->controller->edit($employee->id);
    $buData = $editResult->getData()['bu'];

    // Assert
    expect($buData)->toHaveKey('');
    expect($buData)->toHaveKey('even');
    expect($buData)->toHaveKey('odd');
    expect($buData['even'])->toContain('2024 - 2025');
    expect($buData['odd'])->toContain('2023 - 2024');
});
