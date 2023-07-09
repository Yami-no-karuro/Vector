import './styles/app.scss';

(() => {
  'use-strict';

  document.addEventListener('DOMContentLoaded', () => {

    const logsTable = document.getElementById("logs-table-wrapper");
    if (null !== logsTable) {
      new gridjs.Grid({
        columns: ['ID', 'Domain', 'Content'],
        sort: true,
        server: {
          url: '/api/v1/logs',
          then: data => data.data.entries.map(el => 
            [el.ID, el.domain, el.log]
          ),
          total: data => data.data.total
        },
        pagination: {
          limit: 25,
          server: {
            url: (prev, page, limit) => `${prev}?limit=${limit}&offset=${page * limit}`
          }
        },
        search: {
          server: {
            url: (prev, keyword) => `${prev}?search=${keyword}`
          }
        },
        language: {
          'search': {
            'placeholder': 'Find in entries'
          },
          'pagination': {
            'previous': 'Previus',
            'next': 'Next',
            'showing': 'Displaying',
            'results': () => 'Records'
          }
        }
      }).render(logsTable);
    }

  });

})();