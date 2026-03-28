@props(['href', 'start_date', 'end_date', 'groupby_kvs', 'group_by', 'called_kvs', 'called'])

  <x-form.open method="GET" :action="$href" class="" />

  <div class="row">
    <div class="col">
    </div>
    <div class="col-auto">
      <x-input.date key="start_date" :defaultdt="$start_date" class="" /> 
    </div>
    <div class="col-auto">
      <x-input.date key="end_date" :defaultdt="$end_date" class="" /> 
    </div>
    @if (!empty($groupby_kvs))
    <div class="col-auto">
    <x-input.select key="group_by" :options="$groupby_kvs" :default="old('group_by')" />
    </div>
    @endif
    @if (count($called_kvs ?? []) > 2)
      <div class="col-auto">
    <x-input.select key="called" :options="$called_kvs" :default="old('called')" />
      </div>
    @endif
    <div class="col-auto">
      <x-input.submit label="検索" key="go" class="btn btn-primary btn" />
    </div>
  </div>
<x-form.close />
