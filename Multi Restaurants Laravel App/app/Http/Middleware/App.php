<?php namespace App\Http\Middleware;

use App\Notifications\Newvisit;
use App\Repositories\UploadRepository;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Config;
use mysql_xdevapi\Exception;
use Spatie\Permission\Exceptions\UnauthorizedException;

class App
{

    /**
     * The availables languages.
     *
     * @array $languages
     */
    protected $languages = ['en','fr']; // en, fr

    protected $uploadRepository;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            app()->setLocale(setting('language', app()->getLocale()));
        } else {
            app()->setLocale($request->getPreferredLanguage($this->languages));
        }
        try{
            Carbon::setLocale(app()->getLocale());
            // config(['app.timezone' => setting('timezone')]);

            $this->uploadRepository = new UploadRepository(app());
            $upload = $this->uploadRepository->findByField('uuid', setting('app_logo',''))->first();
            $appLogo = "";
            if ($upload && $upload->hasMedia('app_logo')) {
                $appLogo = $upload->getFirstMediaUrl('app_logo');
            }
            view()->share('app_logo', $appLogo);
        }catch (\Exception $exception){
            echo $exception->getMessage();
    }

        return $next($request);
    }

}