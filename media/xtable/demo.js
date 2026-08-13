var config = {
		height:400,
		title:'IMDB Top 250',
		toolbar:{align:'right',
			buttons:[{text:'Reload',
				icon:'refresh',
			 	script:function(e,id){
					$(e).click(function(){
						$('#'+id).xTable('reload');
					});
				}
				},{
				text:'Add',
				script:function(e,id){
					$(e).click(function(){
						alert('Add');
					});
				}
				}]},
		columns:[{header:'Rank',width:50,name:'rank',align:'center',order:true},
				{header:'Rating',width:50,name:'rating',align:'center',order:true},
				{header:'Title',width:500,name:'movie_name',order:true},
				{header:'Votes',width:80,name:'vote',align:'center',order:true},
				{width:70,align:'center',
					data:function(obj,id,tr){
						var b = $('<button></button>');
						b
						.html('Edit')
						.click(function(){
							alert('Edit, Title:'+obj.movie_name);
						});
						return b;
					}
				},
				{width:70,align:'center',
					data:function(obj,id,tr){
						var b = $('<button></button>');
						b
						.html('Delete')
						.click(function(){
							alert('Delete, Title:'+obj.movie_name);
						});
						return b;
					}
				}],
		
		url:'server.php',
		type:'json',
		pakages:['remote'],
		pagination:{
			message:"Displaying movies %s - %s of %s",
			record_per_page:20
		},
		loading_message:'Loading...',
		order:{column:'rank',type:'ASC'}
};
$('#demo').xTable(config);

function search(){
	var conditions = {
			rank:$('#src_rank').val(),
			rating:$('#src_rating').val(),
			movie_name:$('#src_title').val(),
			vote:$('#src_vote').val()
		};
	$('#demo').xTable('conditions',conditions);
	$('#demo').xTable('reload');
}
