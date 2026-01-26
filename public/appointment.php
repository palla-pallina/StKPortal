<!DOCTYPE html>

<html class="h-full" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Appointment Tabs</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="/_sdk/element_sdk.js"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<style>
    body {
      box-sizing: border-box;
    }
    * {
      font-family: 'DM Sans', sans-serif;
    }
  </style>
<style>@view-transition { navigation: auto; }</style>
<script src="/_sdk/data_sdk.js" type="text/javascript"></script>
</head>
<body class="h-full">
<div class="h-full w-full bg-gradient-to-br from-rose-50 via-white to-amber-50 overflow-auto" id="app">
<div class="max-w-4xl mx-auto p-6"><!-- Header -->
<div class="mb-8">
<h1 class="text-2xl font-bold text-gray-800 mb-2">My Appointments</h1>
<p class="text-gray-500 text-sm">Manage your blood donation appointments</p>
</div><!-- Tabs Container -->
<div class="bg-white rounded-2xl shadow-lg shadow-rose-100/50 overflow-hidden"><!-- Tab Navigation -->
<div class="flex border-b border-gray-100" role="tablist"><button aria-selected="true" class="tab-btn flex-1 py-4 px-4 text-sm font-semibold transition-all duration-300 relative text-rose-600 bg-rose-50/50" data-tab="all" id="tab-all" role="tab"> <span id="tab-all-text">All</span> <span class="ml-2 bg-rose-100 text-rose-600 text-xs px-2 py-0.5 rounded-full" id="count-all">0</span>
<div class="tab-indicator absolute bottom-0 left-0 right-0 h-0.5 bg-rose-500"></div></button> <button aria-selected="false" class="tab-btn flex-1 py-4 px-4 text-sm font-semibold transition-all duration-300 relative text-gray-400 hover:text-gray-600 hover:bg-gray-50" data-tab="to-donate" id="tab-to-donate" role="tab"> <span id="tab-to-donate-text">Pending</span> <span class="ml-2 bg-amber-100 text-amber-600 text-xs px-2 py-0.5 rounded-full" id="count-pending">0</span>
<div class="tab-indicator absolute bottom-0 left-0 right-0 h-0.5 bg-rose-500 opacity-0"></div></button> <button aria-selected="false" class="tab-btn flex-1 py-4 px-4 text-sm font-semibold transition-all duration-300 relative text-gray-400 hover:text-gray-600 hover:bg-gray-50" data-tab="completed" id="tab-completed" role="tab"> <span id="tab-completed-text">Accepted/Ready</span> <span class="ml-2 bg-emerald-100 text-emerald-600 text-xs px-2 py-0.5 rounded-full" id="count-accepted">0</span>
<div class="tab-indicator absolute bottom-0 left-0 right-0 h-0.5 bg-rose-500 opacity-0"></div></button> <button aria-selected="false" class="tab-btn flex-1 py-4 px-4 text-sm font-semibold transition-all duration-300 relative text-gray-400 hover:text-gray-600 hover:bg-gray-50" data-tab="cancelled" id="tab-cancelled" role="tab"> <span id="tab-cancelled-text">Declined</span> <span class="ml-2 bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full" id="count-declined">0</span>
<div class="tab-indicator absolute bottom-0 left-0 right-0 h-0.5 bg-rose-500 opacity-0"></div></button>
</div><!-- Tab Content -->
<div class="p-6"><!-- All Panel -->
<div class="tab-panel" id="panel-all" role="tabpanel">
<div class="space-y-4" id="list-all"></div>
</div><!-- To Donate Panel -->
<div class="tab-panel hidden" id="panel-to-donate" role="tabpanel">
<div class="space-y-4" id="list-pending"></div>
</div><!-- Completed Panel -->
<div class="tab-panel hidden" id="panel-completed" role="tabpanel">
<div class="space-y-4" id="list-accepted"></div>
</div><!-- Cancelled Panel -->
<div class="tab-panel hidden" id="panel-cancelled" role="tabpanel">
<div class="space-y-4" id="list-declined"></div>
</div>
</div>
</div>
</div>
</div>
<script>
    const defaultConfig = {
      tab_all: 'All',
      tab_to_donate: 'To Donate',
      tab_completed: 'Completed',
      tab_cancelled: 'Cancelled',
      primary_color: '#e11d48',
      background_color: '#fff1f2',
      surface_color: '#ffffff',
      text_color: '#1f2937',
      secondary_color: '#f43f5e'
    };

    let activeTab = 'all';

    function switchTab(tabId) {
      activeTab = tabId;
      
      // Update all tab buttons
      document.querySelectorAll('.tab-btn').forEach(btn => {
        const isActive = btn.dataset.tab === tabId;
        const indicator = btn.querySelector('.tab-indicator');
        
        if (isActive) {
          btn.classList.remove('text-gray-400', 'hover:text-gray-600', 'hover:bg-gray-50');
          btn.classList.add('text-rose-600', 'bg-rose-50/50');
          btn.setAttribute('aria-selected', 'true');
          indicator.classList.remove('opacity-0');
        } else {
          btn.classList.add('text-gray-400', 'hover:text-gray-600', 'hover:bg-gray-50');
          btn.classList.remove('text-rose-600', 'bg-rose-50/50');
          btn.setAttribute('aria-selected', 'false');
          indicator.classList.add('opacity-0');
        }
      });
      
      // Update all panels
      document.querySelectorAll('.tab-panel').forEach(panel => {
        const panelTab = panel.id.replace('panel-', '');
        if (panelTab === tabId) {
          panel.classList.remove('hidden');
        } else {
          panel.classList.add('hidden');
        }
      });
    }

    // Add click handlers to tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        switchTab(btn.dataset.tab);
      });
    });

    async function onConfigChange(config) {
      // Update tab labels
      document.getElementById('tab-all-text').textContent = config.tab_all || defaultConfig.tab_all;
      document.getElementById('tab-to-donate-text').textContent = config.tab_to_donate || defaultConfig.tab_to_donate;
      document.getElementById('tab-completed-text').textContent = config.tab_completed || defaultConfig.tab_completed;
      document.getElementById('tab-cancelled-text').textContent = config.tab_cancelled || defaultConfig.tab_cancelled;
    }

    function mapToCapabilities(config) {
      return {
        recolorables: [
          {
            get: () => config.background_color || defaultConfig.background_color,
            set: (value) => {
              config.background_color = value;
              window.elementSdk.setConfig({ background_color: value });
            }
          },
          {
            get: () => config.surface_color || defaultConfig.surface_color,
            set: (value) => {
              config.surface_color = value;
              window.elementSdk.setConfig({ surface_color: value });
            }
          },
          {
            get: () => config.text_color || defaultConfig.text_color,
            set: (value) => {
              config.text_color = value;
              window.elementSdk.setConfig({ text_color: value });
            }
          },
          {
            get: () => config.primary_color || defaultConfig.primary_color,
            set: (value) => {
              config.primary_color = value;
              window.elementSdk.setConfig({ primary_color: value });
            }
          },
          {
            get: () => config.secondary_color || defaultConfig.secondary_color,
            set: (value) => {
              config.secondary_color = value;
              window.elementSdk.setConfig({ secondary_color: value });
            }
          }
        ],
        borderables: [],
        fontEditable: undefined,
        fontSizeable: undefined
      };
    }

    function mapToEditPanelValues(config) {
      return new Map([
        ['tab_all', config.tab_all || defaultConfig.tab_all],
        ['tab_to_donate', config.tab_to_donate || defaultConfig.tab_to_donate],
        ['tab_completed', config.tab_completed || defaultConfig.tab_completed],
        ['tab_cancelled', config.tab_cancelled || defaultConfig.tab_cancelled]
      ]);
    }

    // Initialize SDK
    if (window.elementSdk) {
      window.elementSdk.init({
        defaultConfig,
        onConfigChange,
        mapToCapabilities,
        mapToEditPanelValues
      });
    }
  </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9c3ca01a204b4ee7',t:'MTc2OTM5NTAxNS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script>
<script>
(function(){
  const API_URL = '../private/church_appointments_api.php';

  function pad2(n){ return String(n).padStart(2,'0'); }

  function formatDateTime(item){
    // item.scheduled_at: 'YYYY-MM-DD HH:MM:SS' or 'YYYY-MM-DD'
    if(!item.scheduled_at) return '';
    const s = item.scheduled_at.replace(' ', 'T');
    const d = new Date(s);
    if (isNaN(d.getTime())) return item.scheduled_at;
    const options = { year:'numeric', month:'short', day:'2-digit' };
    const dateStr = d.toLocaleDateString(undefined, options);
    const timeStr = d.toLocaleTimeString(undefined, { hour:'2-digit', minute:'2-digit' });
    return `${dateStr} • ${timeStr}`;
  }

  function badge(statusKey){
    // statusKey: pending|accepted|declined
    if(statusKey === 'pending'){
      return { cls:'bg-amber-100 text-amber-700', iconCls:'text-amber-600', wrapCls:'bg-amber-50 border-amber-100' };
    }
    if(statusKey === 'declined'){
      return { cls:'bg-red-100 text-red-700', iconCls:'text-red-600', wrapCls:'bg-red-50 border-red-100' };
    }
    return { cls:'bg-green-100 text-green-700', iconCls:'text-green-600', wrapCls:'bg-green-50 border-green-100' };
  }

  function iconSVG(colorClass){
    return `
      <svg class="w-6 h-6 ${colorClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>`;
  }

  function renderList(containerId, items){
    const el = document.getElementById(containerId);
    if(!el) return;
    el.innerHTML = '';
    if(!items || items.length === 0){
      el.innerHTML = `<div class="p-4 rounded-xl border border-gray-200 bg-gray-50 text-sm text-gray-600">No records found.</div>`;
      return;
    }

    items.forEach(item => {
      const b = badge(item.status_key);
      const title = item.title || item.service || 'Appointment';
      const dt = formatDateTime(item);
      const sub = item.subtitle ? `<p class="text-xs text-gray-500 mt-0.5">${item.subtitle}</p>` : '';
      const statusLabel = item.status_label || item.status_key;

      const card = document.createElement('div');
      card.className = `flex items-center gap-4 p-4 rounded-xl border ${b.wrapCls}`;
      card.innerHTML = `
        <div class="w-12 h-12 rounded-xl flex items-center justify-center ${b.cls.replace('text-','bg-').replace('-700','-100')}">
          ${iconSVG(b.iconCls)}
        </div>
        <div class="flex-1">
          <p class="font-semibold text-gray-800">${title}</p>
          <p class="text-sm text-gray-500">${dt}</p>
          ${sub}
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-medium ${b.cls}">${statusLabel}</span>
      `;
      el.appendChild(card);
    });
  }

  function setCounts(counts){
    if(!counts) return;
    const map = [
      ['count-all', counts.all ?? 0],
      ['count-pending', counts.pending ?? 0],
      ['count-accepted', counts.accepted ?? 0],
      ['count-declined', counts.declined ?? 0],
    ];
    map.forEach(([id,val])=>{
      const el=document.getElementById(id);
      if(el) el.textContent = val;
    });
  }

  async function load(status){
    const url = new URL(API_URL, window.location.href);
    if(status) url.searchParams.set('status', status);
    const res = await fetch(url.toString(), { headers: { 'Accept':'application/json' } });
    const data = await res.json();
    setCounts(data.counts);
    // render all panels from payload so switching tabs is instant
    renderList('list-all', data.lists?.all || []);
    renderList('list-pending', data.lists?.pending || []);
    renderList('list-accepted', data.lists?.accepted || []);
    renderList('list-declined', data.lists?.declined || []);
  }

  // initial load
  load('all').catch(err=>console.error(err));
})();
</script>
</body>
</html>