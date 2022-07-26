<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<div class="prose">
@foreach($data as $log_entry) 
<pre class="language-css">
<code class="language-json text-sm">
{{ json_encode($log_entry->data) }}
</code>
</pre>
@endforeach
</div></div>