(function($){
    
    $.fn.checkout = function( options ){
        
        var settings = {};
        
        var checkout = this;
        
        checkout.step = 1;
        
        function Steps( ) {
            
            this.data = [];
            
            this.add = function( data ){
                this.data.push( { data: data} );
            },
            this.remove = function( ){
                
            },
            this.edit = function( step, data){
                
            },
            this.getInfo = function(step, data ){
                
                for( var key in this.data ){
                    for( var x in this.data[key].data ){
                        if( data == x ){
                            return this.data[key].data[x]
                        }
                    }
                }
            },
            this.setInfo = function( step, item, value){
                for( var key in this.data ){
                    if( step == this.data[key].step ){
                        for (var x in this.data[key].data ){
                            if( item == x ){
                                this.data[key].data[x] = value;
                            }
                        }    
                    }
                }
            }
        };
        
        var steps = new Steps( );
        
        var $container = "";
        
        var el = checkout.selector;
        
        checkout.init = function( ){
            
            settings = $.extend({
                type: "",
                id: 0,
                container: "",
                project_title: "",
                org_name: "",
                steps: [],
                selected: "",
                step: 1,
            }, options);  
            
            //Get Width of the container div.
            
            if( settings.container.length > 0 ){
                $container = settings.container;
            }
            
            if( settings.steps.length == 0 ){
                // call destroy method
            }else{
                $.each( settings.steps, function( i ){
                    settings.steps[i].step = (i + 1);
                    
                    //Set positioning for step pages
                    if( i == 0 ){
                        $container
                        .find("[data-step=" + (i + 1) + "]")
                        .css("marginLeft", "0px");
                    }else{
                        $container
                        .find("[data-step=" + (i + 1) + "]")
                        .css("marginLeft", "999999px");    
                    }
                });
                
                checkout.firstStep( );    
            }
        }
        
        checkout.firstStep = function( ){
            
            if( settings.selected != "" ){
                $(el)
                .find("[data-step=1]")
                .find("[data-incentive-id=" + settings.selected + "]")
                .addClass('selected')
                .find(".donation").slideDown( );
            }
                
            $(el).css("display","table");     
        }
        
        checkout.nextStep = function( ){
            
            console.log( steps.data.length );
            
            $(".checkout")
            .find("[data-step=" + (settings.step+1) + "]")
            .animate({
                marginLeft: "0px"
            },"slow");    
            
            $(".checkout")
            .find("[data-step=" + settings.step + "]")
            .animate({
                marginLeft: "-590px"
            }, "slow");
            
            settings.step = 100;
        }
        
        checkout.addData = function( data ){
            
            steps.add( steps.data );
        }
        
        checkout.getData = function( step, item){
            return steps.getInfo(step, item);
        }
        
        checkout.editData = function( step, item, value ){
            steps.setInfo( step, item, value);
        }
        
        checkout.back = function( howMany ){
            
            console.log( step );
            
            var tmp = step - howMany;
            
            if( tmp < 1 ){
                console.log( 'no dice' );
            }else{
                console.log( 'proceed' );
                $(".checkout")
                    .find("[data-step=" + (step-howMany) + "]")
                    .animate({
                        marginLeft: "0px"
                    },"slow");    

                    $(".checkout")
                    .find("[data-step=" + step + "]")
                    .animate({
                        marginLeft: "590px"
                    }, "slow");

                 step--;
            }
        }
        
        checkout.destroy = function( ){
            
            delete steps;
        }
        
        checkout.init( );
        
        return checkout;
        
    }
})(jQuery);
//# sourceMappingURL=checkout.js.map
