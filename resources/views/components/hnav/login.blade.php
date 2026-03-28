@if (config('_env.HNAV_LOGIN_A_LABEL', ''))
  <div class="row mb-4 lg-1">
    <div class="col-sm-12">
      <ul class="nav nav-tabs">

        <x-hnav.link 
          :label="config('_env.HNAV_LOGIN_A_LABEL')" 
          :active="config('_env.HNAV_LOGIN_A_SELF') == 'Y'" 
          :href="config('_env.HNAV_LOGIN_A_URL')" 
        />

        <x-hnav.link 
          :label="config('_env.HNAV_LOGIN_B_LABEL')" 
          :active="config('_env.HNAV_LOGIN_B_SELF') == 'Y'" 
          :href="config('_env.HNAV_LOGIN_B_URL')" 
        />

      </ul>
    </div>
  </div>
@endif