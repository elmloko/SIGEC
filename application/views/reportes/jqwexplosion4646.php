<script type="text/javascript">
	$(document).ready(function () {
		// prepare the data
		
      
		var source =
		{
			datatype: "json",
			datafields: [
			{ name: 'codigo', type: 'string'},
			{ name: 'cite_original', type: 'string' },
			{ name: 'nombre_destinatario', type: 'string' },
			{ name: 'cargo_destinatario', type: 'string' }
		],
		cache: false,
		url: '/ajax/jreporte',
		filter: function()
		{
			// update the grid and send a request to the server.
			$("#jqxgrid").jqxGrid('updatebounddata', 'filter');
		},
		sort: function()
		{
			// update the grid and send a request to the server.
			$("#jqxgrid").jqxGrid('updatebounddata', 'sort');
		},
		root: 'Rows',
		beforeprocessing: function(data)
		{		
			if (data != null)
			{
				source.totalrecords = data[0].TotalRows;					
			}
		}
		};		
		var dataadapter = new $.jqx.dataAdapter(source, {
			loadError: function(xhr, status, error)
			{
				alert(error);
			}
		}
		);
	
		// initialize jqxGrid
		$("#jqxgrid").jqxGrid(
		{		
			source: dataadapter,
			
			filterable: true,
			sortable: true,
			autoheight: true,
			pageable: true,
			virtualmode: true,
			rendergridrows: function(obj)
			{
				return obj.data;    
			},
			columns: [
				{ text: 'Ship Name', datafield: 'codigo', width: 200 },
				{ text: 'Address', datafield: 'cite_original', width: 180 },
				{ text: 'City', datafield: 'nombre_destinatario', width: 100 },
				{ text: 'Country', datafield: 'cargo_destinatario'}
			]
		});
	});
    </script>
</head>
<div>
    <div id="jqxgrid"></div>
</div>