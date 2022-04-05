<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home')->middleware(['auth', 'log.request']);


// Events
Route::group(['middleware' => ['auth', 'log.request']], function() {
    Route::resource('calendarevents','CalendarEventController')->whereNumber('calendarevent');
    Route::get('eventfeed', ['as' => 'eventfeed','uses' => 'CalendarEventController@eventfeed'])->middleware('auth');
//	Route::put('updateEvent', ['as' => 'updateEvent','uses' => 'CalendarEventController@updateEvent'])->middleware('auth');
});

//Route::get('calendarevents', 'CalendarEventController@index')->middleware('auth');
Route::get('calendarevents/add', 'CalendarEventController@createEvent')->middleware('auth');
Route::post('calendarevents/add', 'CalendarEventController@store')->middleware('auth');

// Client Status
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/clientstatus/updateOrder', 'ClientStatusController@updateOrder');
    Route::resource('clientstatus','ClientStatusController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('clientstatus');
});

// Candidate Status
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/candidatestatus/updateOrder', 'CandidateStatusController@updateOrder');
    Route::resource('candidatestatus','CandidateStatusController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('candidatestatus');
});


// Candidate Levels
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/candidatelevels/updateOrder', 'CandidateLevelController@updateOrder');
    Route::resource('candidatelevels','CandidateLevelController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('candidatelevel');
});


// Candidate Ratings
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
    Route::resource('candidateratings','CandidateRatingController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('candidaterating');
	Route::get('/candidateratings/updateOrder', 'CandidateRatingController@updateOrder');
});

// Candidate Availabilities
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/candidateavailabilities/updateOrder', 'CandidateAvailabilityController@updateOrder');
    Route::resource('candidateavailabilities','CandidateAvailabilityController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('candidateavailability');
});

// EE status
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/eestatus/updateOrder', 'EeStatusController@updateOrder');
    Route::resource('eestatus','EeStatusController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('eestatus');
});

// Genders
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/genders/updateOrder', 'GenderController@updateOrder');
    Route::resource('genders','GenderController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('gender');
});

// Locations
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
    Route::resource('locations','LocationController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('location');
});


// Job Status
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/jobstatus/updateOrder', 'JobStatusController@updateOrder');
    Route::resource('jobstatus','JobStatusController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('jobstatus');
});

// Job Application Status
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/jobapplicationstatus/updateOrder', 'JobApplicationStatusController@updateOrder');
    Route::resource('jobapplicationstatus','JobApplicationStatusController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('jobapplicationstatus');
});

// Job Types
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/jobtypes/updateOrder', 'JobTypeController@updateOrder');
    Route::resource('jobtypes','JobTypeController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('jobtype');
});

// Job Titles
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	
	Route::post('/jobtitles',['as' => 'admin.jobtitles.index','uses' => 'JobTitleController@index']);
    Route::resource('jobtitles','JobTitleController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('jobtitle');
});

// Salary Categories
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/salarycategories/updateOrder', 'SalaryCategoryController@updateOrder');
    Route::resource('salarycategories','SalaryCategoryController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('salarycategory');
});

// Public Holidays
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
    Route::resource('publicholidays','PublicHolidayController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('publicholiday');
});

// Event Types
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/eventtypes/updateOrder', 'EventTypeController@updateOrder');
    Route::resource('eventtypes','EventTypeController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('eventtype');
});

// Alias Categories
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/aliascategories/updateOrder', 'AliasCategoryController@updateOrder');
    Route::resource('aliascategories','AliasCategoryController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('aliascategory');
});

// Aliases
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::match(['get', 'post'], '/aliases/search', [
    'as' => 'aliases.search',
    'uses' => 'AliasController@index'
]);	
	
    Route::resource('aliases','AliasController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('alias');
});


// Route::get('/candidates',['as' => 'candidates.index','uses' => 'ClientStatusController@index'])->middleware('auth');
// Route::get('/clients',['as' => 'clients.index','uses' => 'ClientStatusController@index'])->middleware('auth');
// Route::get('/jobs',['as' => 'jobs.index','uses' => 'ClientStatusController@index'])->middleware('auth');

//Route::get('/user',['as' => 'user.index','uses' => 'ClientStatusController@index'])->middleware('auth');
//Route::get('/user/edit/{id}',['as' => 'user.edit','uses' => 'ClientStatusController@edit'])->middleware('auth');
//Route::post('/user/edit/{id}','ClientStatusController@update')->middleware('auth');

// Users
Route::group(['middleware' => ['auth', 'log.request']], function() {
//    Route::resource('users','UserController')->whereNumber('user');
	Route::match(['get', 'post'], '/users/search', [
    'as' => 'users.search',
    'uses' => 'UserController@index'
]);	

    Route::resource('users','UserController')->whereNumber('user');
});

// Teams
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
//    Route::resource('users','UserController')->whereNumber('user');
	Route::match(['get', 'post'], '/teams/search', [
    'as' => 'teams.search',
    'uses' => 'TeamController@index'
]);	

    Route::resource('teams','TeamController', ['prefix'=>'admin', 'as'=>'admin'])->whereNumber('team');
});

// Clients
Route::group(['middleware' => ['auth', 'log.request']], function() {
	Route::post('/clients/search','ClientController@index');
	Route::get('/clients/search','ClientController@index');
//	Route::post('/clients/search','ClientController@search');
//	Route::get('/clients/search','ClientController@search');
      Route::resource('clients','ClientController')->whereNumber('client');
	Route::get('/clients/{modelid}/fileupload','ClientController@fileupload')->whereNumber('modelid');
	Route::post('/clients/{modelid}/fileupload','ClientController@fileupload')->whereNumber('modelid');
	Route::get('/clients/{clientid}/clientcontacts/create','ClientContactController@create')->whereNumber('clientid');
	Route::post('/clients/{clientid}/clientcontacts/create',['as' => 'clientcontacts.store','uses' => 'ClientContactController@store'])->whereNumber('clientid');
	
	Route::get('/clients/{id}/jobapplications','ClientController@getJobApplications')->whereNumber('id');	
});

// Client Contacts
Route::group(['middleware' => ['auth', 'log.request']], function() {
    Route::resource('clientcontacts','ClientContactController')->except(['create','store'])->whereNumber('clientcontact');
	
});
//	Route::get('/clients/{id}/clientcontacts/create','ClientContactController@create')->middleware(['auth'])->whereNumber('id');
//	Route::post('/clients/{id}/clientcontacts/create','ClientContactController@store')->middleware(['auth'])->whereNumber('id');

Route::group(['middleware' => ['auth', 'log.request']], function() {
    Route::resource('clientnotes','ClientAgencynoteController')->except(['create','store'])->whereNumber('clientnote');
	
});
	Route::get('/clients/{id}/notes/create',['as' => 'clientnotes.create','uses' => 'ClientAgencynoteController@create'])->middleware(['auth', 'log.request'])->whereNumber('id');
	Route::post('/clients/{id}/notes/create',['as' => 'clientnotes.store','uses' => 'ClientAgencynoteController@store'])->middleware(['auth', 'log.request'])->whereNumber('id');
//	Route::get('/clientnotes/{id}',['as' => 'clientnote.show','uses' => 'ClientController@noteshow'])->middleware(['auth']);
//    Route::get('/clientnotes/{id}/edit',['as' => 'clientnote.edit','uses' => 'ClientController@noteedit'])->middleware(['auth']);

//	Route::get('/clientcontacts/{id}',['as' => 'clientcontacts.show','uses' => 'ClientContactController@show'])->middleware(['auth']);
//Route::get('/clientcontacts/{id}/edit',['as' => 'clientcontacts.edit','uses' => 'ClientContactController@edit'])->middleware(['auth']);
//Route::post('/clientcontacts/{id}/edit','ClientContactController@update')->middleware(['auth']);

//Jobs
Route::group(['middleware' => ['auth', 'log.request']], function() {
	Route::post('/jobs/search','JobAdController@index');
	Route::get('/jobs/search','JobAdController@index');
    Route::resource('jobs','JobAdController')->except(['create','store'])->whereNumber('job');
	Route::get('/jobs/{modelid}/fileupload','JobAdController@fileupload')->whereNumber('modelid');
	Route::post('/jobs/{modelid}/fileupload','JobAdController@fileupload')->whereNumber('modelid');

	Route::get('/clients/{id}/jobs/create','JobAdController@create')->middleware(['auth'])->whereNumber('id');
	Route::post('/clients/{id}/jobs/create','JobAdController@store')->middleware(['auth'])->whereNumber('id');

	
});

//Candidates
Route::group(['middleware' => ['auth', 'log.request']], function() {
	Route::post('/candidates/search','CandidateController@index');
	Route::get('/candidates/search','CandidateController@index');
    Route::resource('candidates','CandidateController')->whereNumber('candidate');
	Route::post('/candidates/{modelid}/avatarupload','CandidateController@avatarupload')->whereNumber('modelid');
	Route::get('/candidates/{modelid}/fileupload','CandidateController@fileupload')->whereNumber('modelid');
	Route::post('/candidates/{modelid}/fileupload','CandidateController@fileupload')->whereNumber('modelid');
	Route::get('/candidatesarchive','CandidateController@bulkArchive');
	Route::post('/candidatesarchive','CandidateController@bulkArchive');
	Route::get('/candidatemerge/{idtokeep}/{idtomerge}','CandidateController@mergeCandidate')->whereNumber('idtokeep')->whereNumber('idtomerge')->name('candidatemerge');;
	
});


//Agency Notes
Route::group(['middleware' => ['auth', 'log.request']], function() {
	Route::get('/notes/{model}/{modelid}',['as' => 'notes.edit','uses' => 'AgencynoteController@edit'])->where('model', 'candidates|jobs')->whereNumber('modelid');
	Route::post('/notes/{model}/{modelid}',['as' => 'notes.update','uses' => 'AgencynoteController@update'])->where('model', 'candidates|jobs')->whereNumber('modelid');
});

Route::group(['middleware' => ['auth', 'log.request']], function() {
	Route::get('testcand','CandidateController@test');
	Route::post('candidatelist','CandidateController@getAutocompleteData'); 
	Route::post('joblist','JobAdController@getAutocompleteData'); 
	Route::post('clientlist','ClientController@getAutocompleteData'); 
});	
	
// Search checkboxes
Route::group(['middleware' => ['auth', 'log.request']], function() {
	Route::get('/setCheckBox',['as' => 'setcheckbox','uses' => 'SearchController@setcheckbox']);
});

// Searches
Route::group(['middleware' => ['auth', 'log.request']], function() {
	Route::get('/savedsearches',['as' => 'savedsearches.index','uses' => 'SearchController@index']);
	Route::get('/savedsearch/{searchid}',['as' => 'savedsearch.edit','uses' => 'SearchController@edit'])->whereNumber('searchid');
	Route::patch('/savedsearch/{searchid}',['as' => 'savedsearch.update','uses' => 'SearchController@update'])->whereNumber('searchid');
    Route::delete('/savedsearch/{id}/delete',['as' => 'savedsearch.destroy','uses' => 'SearchController@destroy'])->whereNumber('id');
});


// Job Applications

Route::group(['middleware' => ['auth', 'log.request']], function() {
	Route::get('/linkjobs/{matchid}',['as' => 'linkjobs.linkCandMultipleJobs','uses' => 'JobApplicationController@linkCandMultipleJobs'])->whereNumber('matchid');
	Route::get('/linkcandidates/{matchid}',['as' => 'linkJobMultipleCandidates.index','uses' => 'JobApplicationController@linkJobMultipleCandidates'])->whereNumber('matchid');
    Route::resource('jobapplications','JobApplicationController')->except(['create','store','index'])->whereNumber('jobapplication');
	Route::get('/linkcandidatejob/{candidate_id}/{jobad_id}',['as' => 'linkcandidatejob','uses' => 'JobApplicationController@LinkCandidateJob'])->whereNumber('candidate_id')->whereNumber('jobad_id');

	Route::get('/linkcandidateclient/{candidate_id}/{client_id}',['as' => 'linkcandidateclient','uses' => 'JobApplicationController@LinkCandidateClient'])->whereNumber('candidate_id')->whereNumber('client_id');

	Route::get('/emailcvstoclient/{matchid}',['as' => 'emailcvstoclient.create','uses' => 'JobApplicationController@emailCvsToClient_create'])->where('matchid', '(a|j)_[0-9]+');
	Route::post('/emailcvstoclient/{matchid}',['as' => 'emailcvstoclient.store','uses' => 'JobApplicationController@emailCvsToClient_store'])->where('matchid', '(a|j)_[0-9]+');

	Route::get('/emailjobspecstocandidate/{matchid}',['as' => 'emailjobspecstocandidate.create','uses' => 'JobApplicationController@emailJobSpecsToCandidate_create'])->whereNumber('matchid');
	Route::post('/emailjobspecstocandidate/{matchid}',['as' => 'emailjobspecstocandidate.createP','uses' => 'JobApplicationController@emailJobSpecsToCandidate_create'])->whereNumber('matchid');
	Route::post('/emailjobspecstocandidate/{matchid}/mailed',['as' => 'emailjobspecstocandidate.store','uses' => 'JobApplicationController@emailJobSpecsToCandidate_store'])->whereNumber('matchid');

});

Route::group(['middleware' => ['auth', 'log.request', 'role:Bulk Email Candidates']], function() {

	Route::get('/emailjobspectocandidates/{matchid}',['as' => 'emailjobspectocandidates.create','uses' => 'JobApplicationController@emailJobSpecToCandidates_create'])->whereNumber('matchid');
	Route::post('/emailjobspectocandidates/{matchid}',['as' => 'emailjobspectocandidates.createP','uses' => 'JobApplicationController@emailJobSpecToCandidates_create'])->whereNumber('matchid');
	Route::post('/emailjobspectocandidates/{matchid}/mailed',['as' => 'emailjobspectocandidates.store','uses' => 'JobApplicationController@emailJobSpecToCandidates_store'])->whereNumber('matchid');
});


Route::group(['middleware' => ['auth', 'log.request']], function() {
	Route::get('/emailcvtoclients/{matchid}',['as' => 'emailcvtoclients.create','uses' => 'JobApplicationController@emailCvToMultipleClients_create'])->whereNumber('matchid');
	Route::post('/emailcvtoclients/{matchid}',['as' => 'emailcvtoclients.createP','uses' => 'JobApplicationController@emailCvToMultipleClients_create'])->whereNumber('matchid');
	Route::post('/emailcvtoclients/{matchid}/mailed',['as' => 'emailcvtoclients.store','uses' => 'JobApplicationController@emailCvToMultipleClients_store'])->whereNumber('matchid');
});
	

//store a push subscriber.
Route::post('/push','PushController@store');
//make a push notification.
Route::get('/push','PushController@push')->name('push');


//Route::get('/home', function() {
 //   return view('home');
//})->name('home')->middleware('auth');



Route::group([
    'prefix' => config('email_log.routes_prefix', ''),
    'middleware' => array_filter(['web',config('email_log.access_middleware', null)]),
], function(){
    Route::get('/emails', ['as' => 'loggedemail', 'uses' => 'LoggedEmailController@index']);
    Route::post('/emails/delete', ['as' => 'loggedemail.delete-old', 'uses' => 'LoggedEmailController@deleteOldEmails']);
    Route::get('/emails/{id}/attachment/{attachment}', ['as' => 'loggedemail.fetch-attachment', 'uses' => 'LoggedEmailController@fetchAttachment'])->whereNumber('id')->whereNumber('attachment');
});

Route::group(['middleware' => ['auth', 'log.request']], function() {
    Route::get('/emails/{id}', ['as' => 'email.show', 'uses' => 'LoggedEmailController@show'])->whereNumber('id');
});


Route::group([
    'prefix' => config('email_log.routes_prefix', ''),
], function(){
    //webhooks events
    Route::post('/email/webhooks/event', ['as' => 'email-log.webhooks', 'uses' => 'LoggedEmailController@createEvent']);
});


// Reports
Route::group(['middleware' => ['auth', 'log.request', 'role:Admin|Data Exporter']], function() {
	Route::get('/reports/candidates',['uses' => 'ReportController@candidates']);
	Route::post('/reports/candidates',['uses' => 'ReportController@candidates']);

	Route::get('/reports/clients',['uses' => 'ReportController@clients']);
	Route::post('/reports/clients',['uses' => 'ReportController@clients']);

	Route::get('/reports/jobs',['uses' => 'ReportController@jobs']);
	Route::post('/reports/jobs',['uses' => 'ReportController@jobs']);
});	

Route::group(['middleware' => ['auth', 'log.request', 'role:Admin']], function() {
	Route::get('/reports/cvsent',['uses' => 'ReportController@cvsent']);
	Route::post('/reports/cvsent',['uses' => 'ReportController@cvsent']);

	Route::get('/reports/candidatehistory',['uses' => 'ReportController@candidatehistory']);
	Route::post('/reports/candidatehistory',['uses' => 'ReportController@candidatehistory']);

	Route::get('/reports/jobhistory',['uses' => 'ReportController@jobhistory']);
	Route::post('/reports/jobhistory',['uses' => 'ReportController@jobhistory']);
});	

Route::group(['middleware' => ['auth', 'log.request', 'role:Data Exporter']], function() {
	Route::get('/reports/useractivity',['uses' => 'ReportController@useractivity']);
	Route::post('/reports/useractivity',['uses' => 'ReportController@useractivity']);

	Route::get('/reports/staticalerts',['uses' => 'ReportController@staticalerts']);
	Route::post('/reports/staticalerts',['uses' => 'ReportController@staticalerts']);

	Route::get('/reports/linkedcandidates',['uses' => 'ReportController@linkedcandidates']);
	Route::post('/reports/linkedcandidates',['uses' => 'ReportController@linkedcandidates']);
});	

	


Route::get('/file/{id}/delete', ['as'=>'file.delete', 'uses'=>'DataFileController@fileDestroy'])->middleware('auth')->where('id', '[0-9]+_[a-z]+_[0-9]+');
Route::get('/file/{id}/download', ['as'=>'file.download', 'uses'=>'DataFileController@download'])->middleware('auth')->where('id', '[0-9]+_[a-z]+_[0-9]+');

Route::post('/file/{id}/rename', ['as'=>'file.rename', 'uses'=>'DataFileController@rename'])->middleware('auth')->where('id', '[0-9]+_[a-z]+_[0-9]+');

//->whereNumber('id')->whereAlpha('model')->whereNumber('modelid');
// File upload
//Route::get('/file/upload','FileUploadController@fileCreate')->middleware('auth');
//Route::post('/file/upload/store','FileUploadController@fileStore')->middleware('auth');
//Route::post('/file/{$id}/delete','FileUploadController@fileDestroy')->middleware('auth');
//Route::get('/file/{$id}/download','DataFileController@download')->middleware(['auth', 'log.request'])->whereNumber('id');

// Client Contact
/* Route::get('/salarycategories',['as' => 'admin.salarycategories.index','uses' => 'SalaryCategoryController@index'])->middleware(['auth', 'role:Admin']);
Route::get('/salarycategories/add',['as' => 'admin.salarycategories.add','uses' => 'SalaryCategoryController@create'])->middleware(['auth', 'role:Admin']);
Route::post('/salarycategories/add','SalaryCategoryController@store')->middleware(['auth', 'role:Admin']);
Route::get('/salarycategories/{id}/edit',['as' => 'admin.salarycategories.edit','uses' => 'SalaryCategoryController@edit'])->middleware(['auth', 'role:Admin']);
Route::post('/salarycategories/{id}/edit','SalaryCategoryController@update')->middleware(['auth', 'role:Admin']);
Route::delete('/salarycategories/{id}/delete',['as' => 'admin.salarycategories.destroy','uses' => 'SalaryCategoryController@destroy'])->middleware(['auth', 'role:Admin']);
Route::get('/salarycategories/updateOrder', 'SalaryCategoryController@updateOrder');
Route::get('/salarycategories/{id}',['as' => 'admin.salarycategories.show','uses' => 'SalaryCategoryController@show'])->middleware(['auth', 'role:Admin']);
*/

