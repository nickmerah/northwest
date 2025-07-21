<?php set_time_limit(0);

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RemedialController;
use App\Http\Controllers\ClearanceController;
use App\Http\Controllers\SchoolInfoController;
use App\Http\Controllers\PortalPaymentController;
use App\Http\Controllers\RemedialPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [SchoolInfoController::class, 'index']);
Route::get('portallogin', [LoginController::class, 'showLoginForm'])->name('portallogin');
Route::get('aportallogin', [LoginController::class, 'ashowLoginForm'])->name('aportallogin');
Route::get('forgotpass', [LoginController::class, 'showForgotPasswordForm'])->name('forgotpass');


Route::get('clearancelogin', [LoginController::class, 'showClearanceLoginForm'])->name('clearancelogin');
Route::get('clearanceforgotpass', [LoginController::class, 'showClearanceForgotPasswordForm'])->name('clearanceforgotpass');

Route::get('logout', [LoginController::class, 'logout']);
Route::get('plogout', [LoginController::class, 'plogout']);
Route::get('rlogout', [LoginController::class, 'rlogout']);

Route::get('/portalverify', [LoginController::class, 'showLoginVerificationForm'])->name('portalverify');
Route::post('/portalverify', [RegisterController::class, 'LoginVerification'])->name('portalverify');
Route::get('/portalregister', [RegisterController::class, 'showportalRegistrationForm'])->name('portalregister');
Route::post('/portalregister', [RegisterController::class, 'portalRegister']);

Route::get('/clearanceregister', [RegisterController::class, 'showClearanceRegistrationForm'])->name('clearanceregister');
Route::post('/clearanceregister', [RegisterController::class, 'clearanceRegister']);
Route::get('/get-departments/{programme_id}', [RegisterController::class, 'getDepartments']);
Route::get('/get-levels/{programme_id}', [RegisterController::class, 'getLevels']);

Route::get('/download-passports', [SchoolInfoController::class, 'downloadAllPassports']);


Route::middleware('throttle:5,1')->group(function () {
    Route::post('clearancelogin', [LoginController::class, 'clearancelogin']);
    Route::post('portallogin', [LoginController::class, 'registerlogin']);
    Route::post('aportallogin', [LoginController::class, 'aregisterlogin']);
    Route::post('remediallogin', [LoginController::class, 'remediallogin']);
    Route::post('forgotPass', [LoginController::class, 'forgotpass'])->name('forgotPass');
    Route::post('forgotPassClearance', [LoginController::class, 'forgotpassClearance'])->name('forgotPassClearance');
});

Route::middleware('checkUserSession')->group(function () {
    Route::get('/clearanceDashboard', [ClearanceController::class, 'home']);
    Route::get('/clearanceFees', [ClearanceController::class, 'clearancefees']);
    Route::get('/clearancePayments', [ClearanceController::class, 'phistory']);
    Route::get('/viewfeepack/{packid}', [ClearanceController::class, 'viewFeePack'])->name('viewfeepack');
    Route::get('/paypacknow/{packid}', [PaymentController::class, 'payPackNow'])->name('paypacknow');
    Route::get('/viewfee/{rrr}', [ClearanceController::class, 'viewFees'])->name('viewfee');
    Route::get('/processfee/{rrr}', [PaymentController::class, 'processFees'])->name('processfee');
    Route::get('/remitaresponse', [PaymentController::class, 'remitaresponse'])->name('remitaresponse');
    Route::get('/checkpayment', [PaymentController::class, 'checkpayment'])->name('checkpayment');
    Route::get('/phistory', [ClearanceController::class, 'phistory'])->name('phistory');
    Route::get('/printreceipt/{trans_no}', [ClearanceController::class, 'printReceipt'])->name('printreceipt');
});

Route::middleware('checkLoginUserSession')->group(function () {
    Route::get('/portalDashboard', [PortalController::class, 'home']);
    Route::get('/profile', [PortalController::class, 'biodata']);
    Route::get('/passport', [PortalController::class, 'passport']);
    Route::post('/updateprofile', [PortalController::class, 'biodataupdate'])->name('updateprofile');
    Route::post('/updatepassport', [PortalController::class, 'passportupdate'])->name('updatepassport');
    Route::get('/ofees', [PortalController::class, 'ofee']);
    Route::post('/ofees', [PortalController::class, 'previewofee'])->name('previewofee');
    Route::post('/saveofees', [PortalPaymentController::class, 'saveofees'])->name('saveofees');
    Route::get('/fees', [PortalController::class, 'fee'])->name('fees');
    Route::get('/sfees', [PortalController::class, 'sfee']);
    Route::get('/bfees', [PortalController::class, 'bfee']);
    Route::get('/bpfee', [PortalController::class, 'bpfee']);
    Route::post('/ppfee', [PortalController::class, 'pfee'])->name('ppfee');
    Route::post('/fees', [PortalController::class, 'previewfee'])->name('previewfee');
    Route::post('/pfees', [PortalController::class, 'previewpfee'])->name('previewpfee');
    Route::get('/viewofee/{rrr}', [PortalController::class, 'viewOfees'])->name('viewofee');
    Route::get('/processfees/{rrr}', [PortalPaymentController::class, 'processFees'])->name('processfees');
    Route::get('/remitaresponses', [PortalPaymentController::class, 'remitaresponse'])->name('remitaresponses');
    Route::get('/checkpayments', [PortalPaymentController::class, 'checkpayment'])->name('checkpayments');
    Route::get('/pfhistory', [PortalController::class, 'phistory'])->name('pfhistory');
    Route::get('/printfreceipt/{trans_no}', [PortalController::class, 'printReceipt'])->name('printfreceipt');
    Route::post('/fees', [PortalController::class, 'previewfee'])->name('previewfee');
    Route::post('/savenewfees', [PortalPaymentController::class, 'savenewfees'])->name('savenewfees');
    Route::post('/savefees', [PortalPaymentController::class, 'savefees'])->name('savefees');
    Route::post('/savesfees', [PortalPaymentController::class, 'savesfees'])->name('savesfees');
    Route::post('/savebfees', [PortalPaymentController::class, 'savebfees'])->name('savebfees');
    Route::post('/savepfees', [PortalPaymentController::class, 'savepfees'])->name('savepfees');
    Route::get('/viewschfee/{rrr}', [PortalController::class, 'viewfees'])->name('viewschfee');
    Route::get('/viewsfee/{rrr}', [PortalController::class, 'viewsfees'])->name('viewsfee');
    Route::get('/hostels', [PortalController::class, 'makeHostelPayment'])->name('hostelpayment');
    Route::get('/reserveRoom', [PortalController::class, 'roomAvailability'])->name('reserveRoom');
    Route::post('/hostels/{roomId}/allocate-room', [PortalController::class, 'allocateRoom'])->name('hostels.allocateRoom');
    Route::get('/myRoom', [PortalController::class, 'reservedRoom']);
    Route::post('/payment/{hostel_id}/{of_id}', [PortalPaymentController::class, 'payHostelFee'])->name('payment.route')->where(['hostel_id' => '[0-9]+', 'of_id' => '[0-9]+']);
    Route::get('/printReservation', [PortalController::class, 'printHostelReservation'])->name('printReservation');

    Route::get('/courses', [PortalController::class, 'getcourses']);
    Route::post('/courses', [PortalController::class, 'previewcourse'])->name('previewcourse');
    Route::post('/savecourses', [PortalController::class, 'savecourses'])->name('savecourses');
    Route::get('/viewcourse', [PortalController::class, 'viewcourses']);
    Route::post('/removecourse', [PortalController::class, 'removecourses']);
    Route::get('/creghistory', [PortalController::class, 'coursereghistory']);
    Route::get('/printcreg/{sess}', [PortalController::class, 'printCourseReg']);
    
    Route::get('/testrrr', [PortalPaymentController::class, 'testrrr'])->name('testrrr');
});


Route::get('remediallogin', [LoginController::class, 'showRemedialLoginForm'])->name('remediallogin');
Route::middleware('checkRemedialUserSession')->group(function () {
    Route::get('/remedialDashboard', [RemedialController::class, 'home']);
    Route::get('/makepayment', [RemedialController::class, 'makepayment']);
    Route::post('/rfees', [RemedialController::class, 'previewfee'])->name('rfees');
    Route::post('/payremedialfees', [RemedialPaymentController::class, 'payNow'])->name('payremedialfees');
    Route::post('/payadditionalremedialfees', [RemedialPaymentController::class, 'payNowAgain'])->name('payadditionalremedialfees');
    Route::get('/viewremedialfee/{rrr}', [RemedialController::class, 'viewfees'])->name('viewremedialfee');
    Route::get('/processremedialfees/{rrr}', [RemedialPaymentController::class, 'processFees'])->name('processremedialfees');
    Route::get('/rphistory', [RemedialController::class, 'phistory'])->name('rphistory');
    Route::get('/printrreceipt/{trans_no}', [RemedialController::class, 'printReceipt'])->name('printrreceipt');
    Route::get('/rcheckpayments', [RemedialPaymentController::class, 'checkpayment'])->name('rcheckpayments');
    Route::get('/rcourses', [RemedialController::class, 'coursereg']);
    Route::post('/rcourses', [RemedialController::class, 'regcourse'])->name('rcourses');
    Route::get('/printcourses', [RemedialController::class, 'printCourse'])->name('printcourses');
});

/*Route::get('/clear', function () {
    Artisan::call('make:mail TestMail');
    return 'Cache cleared!';
});
*/
Route::get('/send-test-email', function () {
    $dmail = "nickmerah@gmail.com";
    $details = [
        'title' => 'Password Reset Info from DSPG Student Portal',
        'body' => "Mail sent",
        'info' => "Password Reset Info for DSPG Student Portal is as follows:",
        'email' => $dmail,
        'passkey' => "1111",
        'appno' => "TEST111"
    ];

    print_r($details);

    Mail::to($dmail)->send(new MailNotifier($details));
});
 
