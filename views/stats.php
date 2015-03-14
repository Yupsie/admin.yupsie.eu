<?php //	namespace Views;

/**
 *	class V_Stats
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class V_Stats extends V_Main {

	/**
	 *	function setContent
	 *	Put the content in the view
	 *
	 *	@access public
	 *	@param array aData
	 *	@return void
	 */
	public function setContent($aData = array()) {
		$this->sData = '
		<section>
			<article>
				<h2>' . CMS_STATS_VISITS . ': ' . (isset($aData['count'])?$aData['count']:'') . '</h2>
				<script type="text/javascript">';
		$this->sData .= '
					Highcharts.setOptions({
						colors: ["#a1a1c1",
								"#ffffff",
								"#2b4572",
								"#10bcff",
								"#1072e6",
								"#10bcff",
								"#1072e6",
								"#10bcff",
								"#f59849",
								"#c41f36",
								"#000020"]
					});
					$(function () {
				        $("#container").highcharts({
				            chart: {
								renderTo: "container",
								backgroundColor: "rgba(255, 255, 255, 0.0)",
								style: {
									fontFamily: \'"Josefin","Helvetica","Trebuchet MS",sans-serif\'
								}
				            },
				            title: {
				                text: "",
								style: {
									fontFamily: \'"Josefin","Helvetica","Trebuchet MS",sans-serif\'
								}
				            },
							credits: {
								enabled: false
							},
							xAxis: {
								categories: [
									"Nov \'13", "Dec \'13", 
									"Jan \'14", "Feb \'14", "Mrt \'14", "Apr \'14", "Mei \'14", "Jun \'14", "Jul \'14", "Aug \'14", "Sep \'14", "Okt \'14", "Nov \'14", "Dec \'14",
									"Jan \'15", "Feb \'15", "Mrt \'15", "Apr \'15", "Mei \'15", "Jun \'15", "Jul \'15", "Aug \'15", "Sep \'15", "Okt \'15", "Nov \'15", "Dec \'15", 
									"Jan \'16", "Feb \'16", "Mrt \'16", "Apr \'16", "Mei \'16", "Jun \'16", "Jul \'16", "Aug \'16", "Sep \'16", "Okt \'16", "Nov \'16", "Dec \'16"
								],
								gridLineColor: "#aaaaff"
							},
							yAxis: {
								min: 0,
								title: {
									text: ""
								},
								gridLineColor: "#bbbbff"
							},
				            tooltip: {
				                formatter: function() {
				                    var s;
				                    if (this.point.name) { // the pie chart
				                        s = ""+
				                            this.point.name + ": " + this.y + " ' . CMS_STATS_VISITORS . '";
				                    } else {
				                        s = ""+
				                            this.x  +": "+ this.y;
				                    }
				                    return s;
				                },
								style: {
									fontFamily: \'"Josefin","Helvetica","Trebuchet MS",sans-serif\'
								}
				            },
							legend: {
								layout: "horizontal",
								backgroundColor: "#ddddff",
								borderRadius: "0",
								borderWidth: "2",
								borderColor: "#ddddff",
								itemStyle: {
									fontFamily: \'"Josefin","Helvetica","Trebuchet MS",sans-serif\'
								}
							},
							loading: {
								hideDuration: 100,
								showDuration: 100
							},
							plotOptions: { 
								column: {
									pointPadding: 0.2,
									borderWidth: 0,
									borderColor: "#aaaaff",
									animation: true
								}
							},
				            labels: {
				                items: [{
				                    html: "' . CMS_STATS_BROWSERS . '",
				                    style: {
				                        left: "40px",
				                        top: "8px",
				                        color: "#444444",
										fontFamily: \'"Josefin","Helvetica","Trebuchet MS",sans-serif\'
				                    }
				                }]
				            },
				            series: [';
		$i = 0;
		$aSum = array();
		if (isset($aData['series'])) {

			asort($aData['series']);
			foreach ($aData['series'] as $sKey => $aValue) {

				$this->sData .= ($i == 0?'':',') . '
							{
								type: "column",
								name: "' . $sKey . '",
								data: [' . implode(',', $aValue) . ']
							}';
				$i++;
			}
		}
		foreach ($aData['series'] as $sBrowser => $aValue) {

			foreach ($aValue as $sDate => $aValue) {

				if (!isset($aSum[$sDate])) $aSum[$sDate] = 0;

				$aSum[$sDate] += $aData['series'][$sBrowser][$sDate];
			}
		}
		ksort($aSum);
		$this->sData .= ',
							{
				                type: "spline",
				                name: "' . CMS_STATS_TOTAL . '",
				                data: [' . implode(',', $aSum) . '],
				                marker: {
				                	lineWidth: 2,
				                	lineColor: Highcharts.getOptions().colors[0],
				                	fillColor: Highcharts.getOptions().colors[1]
				                }
				            }, {
				                type: "pie",
								name: "' . CMS_STATS_BROWSERS . '",
								data: [';
		$i = 0;
		asort($aData['pie'][date('Y')]);
		foreach ($aData['pie'][date('Y')] as $sKey => $sValue) {
			$this->sData .= ($i == 0?'':',') . '
								{
									name:"' . $sKey . '", 
									y:' . $sValue . ',
				   					color: Highcharts.getOptions().colors[' . $i . ']
								}';
			$i++;
		}
		$this->sData .= '],
				                center: [100, 80],
				                size: 100,
				                showInLegend: false,
				                dataLabels: {
				                    enabled: false
				                }
				            }]
				        });
				    });
				</script>
				<div id="container" style="width:100%;display:inline-block;"></div>
			</article>
		</section>';
	}
}