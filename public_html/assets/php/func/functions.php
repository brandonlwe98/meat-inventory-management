<?php
$server_hostname = 'localhost';
$server_username = 'ywxfyvndyp';
$server_password = '6Fj843RQHK';
$server_db_name = 'ywxfyvndyp';
function run_query($query, $info = false){
    $conn = new mysqli($GLOBALS['server_hostname'],$GLOBALS['server_username'],$GLOBALS['server_password'],$GLOBALS['server_db_name']);
    if ($conn->connect_error){
        die('Connection Failed: ' . $conn->connect_error);
    }
    $result = $conn->query($query);
    if (gettype($result) != 'boolean'){
        if ($result->num_rows > 0){
            $i = 0;
            while ($row = $result->fetch_assoc()){
                $info[$i] = $row;
                $i++;
            }
        }
    } else {
        if ($result === true){
            $info = $conn->insert_id;
            if (!$info){
                $info = true;
            }
        } else {
            $info = false;
        }
    }
    $conn->close();
    return $info;
}
?>
<?php
function to_safe_var($unsafe_var){
    if (is_array($unsafe_var)){
        foreach ($unsafe_var as $k=>$v){
            $unsafe_var[$k] = to_safe_var($v);
        }
        unset($k);
        unset($v);
        return $unsafe_var;
    } else {
        $conn = new mysqli($GLOBALS['server_hostname'],$GLOBALS['server_username'],$GLOBALS['server_password'],$GLOBALS['server_db_name']);
        if ($conn->connect_error){
            die('Connection Failed: ' . $conn->connect_error);
        }
        $safe_var = $conn->real_escape_string($unsafe_var);
        $conn->close();
        return $safe_var;
    }
}
if (isset($_GET)){
    foreach ($_GET as $k=>$v){
        $_GET[$k] = to_safe_var($v);
    }
    unset($k);
    unset($v);
}
if (isset($_POST)){
    foreach ($_POST as $k=>$v){
        $_POST[$k] = to_safe_var($v);
    }
    unset($k);
    unset($v);
}
?>
<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
$current_datetime = date('Y-m-d H:i:s');
$current_date = date('Y-m-d');
$current_time = date('H:i:s');
?>
<?php
$team_data = array(
    array(
        'id'=>'01',
        'name'=>'Dato\' Sri Ting Teck Sheng',
        'position'=>'Founder & President',
        'display'=>1
    ),
    array(
        'id'=>'02',
        'name'=>'Christina Hew',
        'position'=>'COO (Chief Operating Officer)',
        'display'=>1
    ),
    array(
        'id'=>'03',
        'name'=>'Fong Chit Seng',
        'position'=>'CEO (Chief Executive Officer)',
        'display'=>1
    ),
    array(
        'id'=>'04',
        'name'=>'Prof. Lim Kah Meng',
        'position'=>'Vice President',
        'display'=>1
    ),
    array(
        'id'=>'05',
        'name'=>'Ken Ong',
        'position'=>'Deputy Financial Officer',
        'display'=>1
    ),
    array(
        'id'=>'06',
        'name'=>'Simon Yiek',
        'position'=>'East M\'sia Vice President',
        'display'=>1
    ),
    array(
        'id'=>'07',
        'name'=>'Pauline Wong',
        'position'=>'Chief HR Officer',
        'display'=>1
    ),
    array(
        'id'=>'08',
        'name'=>'Leslie Ting',
        'position'=>'Chief IT Officer',
        'display'=>1
    ),
    array(
        'id'=>'09',
        'name'=>'Cherise Wong',
        'position'=>'Account Manager',
        'display'=>1
    ),
    array(
        'id'=>'10',
        'name'=>'Susan Khoo',
        'position'=>'Admin Manager',
        'display'=>1
    ),
    array(
        'id'=>'11',
        'name'=>'Catz Goh',
        'position'=>'Director of Design',
        'display'=>1
    ),
    array(
        'id'=>'12',
        'name'=>'Jean Goh',
        'position'=>'Director of GFI Biotech',
        'display'=>1
    ),
    array(
        'id'=>'13',
        'name'=>'Nicholas Tee',
        'position'=>'Director of Development',
        'display'=>1
    ),
    array(
        'id'=>'14',
        'name'=>'Zulkiffli Bin Abdul Samad',
        'position'=>'Marketing Manager',
        'display'=>1
    ),
    array(
        'id'=>'15',
        'name'=>'Mohd Suhaizad Bin Sulaiman',
        'position'=>'Security Manager',
        'display'=>1
    ),
    array(
        'id'=>'16',
        'name'=>'Mohd Fizi Bin Saderi',
        'position'=>'Security Manager',
        'display'=>1
    )
);
$events_data = array(
    array(
        'id'=>'',
        'datetime'=>'',
        'title'=>'',
        'display'=>0
    ),
    array(
        'id'=>'2018_12_12_01',
        'datetime'=>'2018-12-12',
        'title'=>'Finaz Seminar in Ipoh',
        'display'=>1
    ),
    array(
        'id'=>'2018_12_13_01',
        'datetime'=>'2018-12-13',
        'title'=>'Finaz Seminar in Penang',
        'display'=>1
    ),
    array(
        'id'=>'2018_12_18_01',
        'datetime'=>'2018-12-18',
        'title'=>'MOA with Solar Energy',
        'display'=>1
    ),
    array(
        'id'=>'2018_12_24_01',
        'datetime'=>'2018-12-24',
        'title'=>'Finaz Seminar in PJ Hee Loi Ton',
        'display'=>1
    ),
    array(
        'id'=>'2018_12_30_01',
        'datetime'=>'2018-12-30',
        'title'=>'Land Pledge Signing Ceremony',
        'display'=>1
    ),
    array(
        'id'=>'2019_01_08_01',
        'datetime'=>'2019-01-08',
        'title'=>'Finaz Seminar in JB',
        'display'=>1
    ),
    array(
        'id'=>'2019_01_12_01',
        'datetime'=>'2019-01-12',
        'title'=>'QMIS Preferred Stock Seminar in PJ Hee Loi Ton',
        'display'=>1
    ),
    array(
        'id'=>'2019_01_12_02',
        'datetime'=>'2019-01-12',
        'title'=>'Charity Event',
        'display'=>1
    ),
    array(
        'id'=>'2019_01_22_01',
        'datetime'=>'2019-01-22',
        'title'=>'Klang Seminar',
        'display'=>1
    ),
    array(
        'id'=>'2019_01_23_01',
        'datetime'=>'2019-01-23',
        'title'=>'Finaz Seminar PJ',
        'display'=>1
    ),
    array(
        'id'=>'2019_01_25_01',
        'datetime'=>'2019-01-25',
        'title'=>'Finaz RCPS in Ipoh',
        'display'=>1
    ),
    array(
        'id'=>'2019_01_27_01',
        'datetime'=>'2019-01-27',
        'title'=>'QMIS Preferred Stock Seminar in PJ Hee Loi Ton',
        'display'=>1
    )
);
$media_data = array(
    array(
        'id'=>'',
        'datetime'=>'',
        'title'=>'',
        'content'=>'',
        'source'=>'',
        'display'=>0
    ),
    array(
        'id'=>'2018_11_01_01',
        'datetime'=>'2018-11-01',
        'title'=>'Yeo: Malaysia can save at least RM47bil over 15 years by being more energy efficient',
        'content'=>array(
            '___TEXTS___KUALA LUMPUR: The nation stands to save a minimum of RM47bil over the next 15 years if becomes more energy efficient, says Minister of Energy, Green Technology, Science, Climate Change and Environment Minister Yeo Bee Yin (pic).
<br><br>
To work towards this, 50 government buildings will be retrofitted with energy efficient lightings and appliances by next year.
<br><br>
"Studies show the potential savings the nation can achieve in improving energy efficiency between 2016-2030 of a minimum of 137,775GWh (Gigawatt hours) will amount to RM46.92bil in savings," she said during her ministerial reply on issues raised during debates on the mid-term review of the Malaysia Eleventh Malaysia Plan on Thursday (Nov 1).
<br><br>
Yeo said that between RM160mil and RM200mil were awarded through Energy Performance Contract (EPC) to retrofit the government buildings.
<br><br>
Under the contract, she said energy service companies will fund works to retrofit the government buildings with energy efficient chillers and LED lighting.
<br><br>
She added the amount of savings on electric bills would be shared between companies and the government.
<br><br>
To further boost energy efficiency, Yeo said that the government is in the process of drafting the Energy Efficiency and Conservation Act, which will be presented to lawmakers for their feedback next year.
<br><br>
"Besides this, we are also reviewing to improve the National Energy Efficiency Action Plan 2016-2025," she added.
<br><br>
Yeo noted that the use of electricity in buildings represent 50% of total electricity use in Malaysia, which held vast potential for energy efficiency and cost saving.'
        ),
        'source'=>array(
            'www.thestar.com.my',
            'https://www.thestar.com.my/news/nation/2018/11/01/yeo-malaysia-can-save-at-least-rm47bil-over-15-years/'
        ),
        'display'=>1
    ),
    array(
        'id'=>'2018_11_02_01',
        'datetime'=>'2018-11-02',
        'title'=>'Germany plans 20% FIT cut for commercial and industrial solar',
        'content'=>array(
            '___TEXTS___With the ruling coalition having agreed to extend additional tenders for PV and wind power, a related, draft bill by the Federal Ministry of Economics has been disclosed. The policy document includes a proposal for a 20% FIT reduction for solar installations ranging in size from 40 kW to 750 kW.
<br><br>
Following the heads of Germany’s CDU, CSU and SPD parties – who form a governing coalition – agreeing to expand renewable energy generation, a related draft bill by the Federal Ministry of Economics has been made public.
<br><br>
The proposed bill includes planned changes in Germany’s renewable energy law – the EEG – and much more, including a 20% FIT cut for rooftop PV systems of 40-750 kW in scale, a segment that was decisive for this year’s strong growth in renewables. If confirmed, the FIT reduction would come into force from January 1.
<br><br>
The proposal has surprised the industry as it targets the segment largely responsible for Germany having hit its 2.5 GW annual new renewable capacity target for the first time in five years this year.
<br><br>
“Prices for photovoltaic modules and photovoltaic systems have dropped sharply in recent months,” said the German government, in a document supporting the proposed FIT cut. “[The] reasons for the drop in prices are persistent oversupply in the world market, and the expiry of the EU anti-dumping and anti-subsidy tariffs on Chinese photovoltaic modules. This has led to an oversupply situation for larger photovoltaic rooftop systems.”
<br><br>
Half the new solar capacity will be hit
<br><br>
The FIT for commercial and industrial PV installations up to 750 kW in size was €0.1068/kWh last month. The proposed reduction would reduce that to €0.0833/kWh from January 1.
<br><br>
“This announcement has been made and, as a result, the over-subsidy is to be corrected by January 1, 2019, by adjusting the value to be applied for … solar [systems] up to and including an installed capacity of 750 kW,” adds the supporting document to the bill. “This value was 10.68 cents per kilowatt-hour in October 2018. The value is lowered to the level of the ground-mounted systems. This value will be set at 8.33 cents per kilowatt-hour on January 1, 2019.”
<br><br>
According to German solar association the BSW, around half the annual newly installed PV capacity will be affected by the proposed reduction.
<br><br>
Existing mechanism already regulates FIT price
<br><br>
“A modest adjustment of the solar power remuneration for new plants would have, in the next few months, [been provided] by the [section] 49 EEG degressive mechanism … itself. The amount of the [newly] planned cuts is incomprehensible,” said the association.
<br><br>
The BSW has urged the government to significantly increase solar targets.
<br><br>
“Germany will only be able to achieve its climate protection goals and avoid penalties for excessive CO2 emissions if the federal government significantly increases its photovoltaic expansion, not only for ground-mounted projects but also for rooftop PV,” the association added.
<br><br>
UK Energy Minister Claire Perry took a swipe at the German government on Wednesday, accusing it of preaching about the need to stop using coal and nuclear while still utilizing both power sources.'
        ),
        'source'=>array(
            'www.pv-magazine.com',
            'https://www.pv-magazine.com/2018/11/02/germany-plans-20-fit-cut-for-commercial-and-industrial-solar/'
        ),
        'display'=>1
    ),
    array(
        'id'=>'2018_11_04_01',
        'datetime'=>'2018-11-04',
        'title'=>'Dubai adds 250MW capacity to fourth phase of solar park',
        'content'=>array(
            '___TEXTS___The total installed capacity for the fourth phase of Mohammed bin Rashid Al Maktoum park will be executed by an Acwa Power-led consortium
<br><br>
Dubai Electricity and Water Authority signed an agreement to add another 250 megawatt capacity to the fourth phase of the Mohammed bin Rashid Al Maktoum Solar Park, one of the largest concentrated solar power projects in the world.
<br><br>
The amendment to the power purchase agreement with Saudi Arabian Acwa Power, which is leading the execution of the fourth phase of the solar project, will raise the total installed power capacity to 950MW.
<br><br>
The developers would install solar panels with a collective capacity of 250MW at a cost of 2.4 US cents per kilowatt hour, one of the world’s lowest, the Dubai government\'s media office said.
<br><br>
A consortium of Acwa and China\'s Shanghai Power, had submitted low bids to generate electricity at 7.3 US cents per kilowatt an hour for the phase.
<br><br>
Mohammed bin Rashid Al Maktoum Solar Park is one the region’s most ambitious renewable energy projects, and will have 5,000MW installed capacity by 2030. The complex is expected to be the largest single-site solar park in the world built on the basis of an independent power producer (IPP) model.
<br><br>
An IPP is an entity that generates power for sale to a public utility. Dubai currently meets 4 per cent of its power needs from solar and the remainder from natural gas. The emirate targets generating 25 per cent of its energy needs from clean resources by 2020, which it wants to scale up to 75 per cent by 2050. The latest capacity addition to the project would raise the total investment in the park to Dh16 billion, according to the government.
<br><br>
The project will combine three technologies - a 600MW parabolic basin complex, a 100MW solar tower as well as 250MW of photovoltaic panels.
<br><br>
In May, the solar park opened the first part of its phase three - a scheme under the execution of Abu Dhabi’s Masdar and France’s EDF Group, with the completion date set for 2020. In an interview during the launch, Saeed Al Tayer, managing director and chief executive of Dewa, confirmed plans for the addition of more CSP capabilities.
<br><br>
He had said at the time that a 300MW CSP plant would be tendered before the first quarter of next year, exceeding the scheme’s targeted installed capacity of 1,000MW by 2020.
<br><br>
The UAE plans to meet 7 per cent of its power needs from solar during that period.
<br><br>
CSP technology, which generates energy from the sun by using mirrors and lenses spread over large areas, is receiving a considerable uptake in the region due to falling costs.
<br><br>
Countries with high solar irradiation such as the UAE and Oman as well as Morocco are at the forefront of adapting to this technology, according to a note by Fitch Solutions consultancy.
<br><br>
CSP differs from PV technology in that the energy it produces is easier to be stored, but the latter has historically been cheaper and easy to use.
<br><br>
Worldwide, CSP solar capacity currently makes up only 8 per cent of total capacity collated, while PV solar power dominates the market due to its easy installation, particularly for small and large-scale projects, according to Fitch Solutions.'
        ),
        'source'=>array(
            'www.thenational.ae',
            'https://www.thenational.ae/business/energy/dubai-adds-250mw-capacity-to-fourth-phase-of-solar-park-1.787748/'
        ),
        'display'=>1
    ),
    array(
        'id'=>'2018_11_09_01',
        'datetime'=>'2018-11-09',
        'title'=>'Solar energy generator for 15 orang asli households',
        'content'=>array(
            '___TEXTS___THE lives of the orang asli in Ayer Denak village in Batu Gajah is a little brighter, thanks to a group of Universiti Teknologi Petronas students.
<br><br>
Under its “Project Revitalise” initiative, 25 university students built a simple solar energy generator to generate energy for 15 households in the village.
<br><br>
Led by project director Chew Jay Shen, the corporate social responsibility (CSR) initiative was part of the students’ requirement to complete their course.
<br><br>
“This initiative served as a platform for the students to use their engineering abilities and thinking skills to develop the rural community by providing a sustainable and affordable source of renewable energy in the form of solar structure.
<br><br>
“The solar structure will be able to power up small electrical appliances and portable lanterns, aiding them in their daily chores and allowing their children to study at night,” he said.
<br><br>
Chew said the villagers were also taught about the mechanism, how to use the solar generator and the cleaning process involved.
<br><br>
SK Air Denak pupils were also exposed to mechanism of the solar panel and how it generates electricity.“We showed the children some solar-powered toy cars and had some fun activities with them.
<br><br>
“We also presented rechargeable lanterns to the villagers.”'
        ),
        'source'=>array(
            'www.thestar.com.my',
            'https://www.thestar.com.my/metro/metro-news/2018/11/09/solar-energy-generator-for-15-orang-asli-households/'
        ),
        'display'=>1
    ),
    array(
        'id'=>'2018_11_09_02',
        'datetime'=>'2018-11-09',
        'title'=>'Sunseap to build 5MW offshore floating solar project near Singapore',
        'content'=>array(
            '___TEXTS___Singapore-based solar develop Sunseap Group is developing one of the world’s largest offshore floating photovoltaic (OFPV) systems, a pilot system standing at 5MW on sea water along the Straits of Johor.
<br><br>
The project, to be located north of Woodlands Waterfront Park, is expected to generate around 6,388 MWh of renewable energy annually, once completed - the equivalent to powering about 1,250 4-room flats. The five-hectare OFPV pilot in Woodlands is expected to be commercially operational early next year, having received support from the Singapore Economic Development Board (EDB).
<br><br>
To date, most floating solar plants in the world have been built on freshwater ponds, lakes or reservoirs, having recently surpassed a global capacity of 1.1GW and having a 400GW gloabl potential, according to a recent World Bank report. However, Sunseap\'s will be one of the first and largest to be located on the sea. Although Singapore is already a hub of floating solar technology with the world’s largest floating PV test-bed at Tengeh Reservoir, it also lacks land, rooftop space and freshwater availability, hence Sunseap\'s venture out to salt water.
<br><br>
Frank Phuan, co-founder and CEO of Sunseap Group, said: “Sunseap is excited to embark on this landmark project which demonstrates Sunseap’s engineering capabilities in research and development. Our floating solar system supports Singapore’s ambition to be a solar hub for Asia, and we hope it will ignite more deployment of alternative methods of tapping solar energy.”
<br><br>
Damian Chan, executive director of Energy, Chemicals & Materials, EDB, said: “Solar is one of the most viable and sustainable clean energy options in Singapore, and we continue to see innovative solar solutions being developed and deployed here. Beyond contributing to Singapore’s energy security and climate change commitments, Sunseap’s offshore floating photovoltaic system will help the clean energy ecosystem and build new, exportable capabilities for potential scale-up across the region.”
<br><br>
Sunseap\'s tests at Tengeh Reservoir so far show that the floating PV systems perform better than typical rooftop solar PV systems in Singapore, due to the cooler temperatures of the reservoir environment.
<br><br>
EDB is currently exploring the possibility of a 100MW floating PV system for private sector consumption at Kranji Reservoir, while the National Water Agency, part of the utilities agency PUB, has announced plans to tender for two FPV power plants for a total of 56.7MW capacity.
<br><br>
Børge Bjørneklett, co-founder of Ocean Sun and the inventor of a new floating solar concept, recently submitted a technical paper showing some of the pioneering R&D work being undertaken in the race to take solar to the open seas. His company has tested a 100kWp installation in the sea outside Bergen, Norway.
<br><br>
In February this year, a Dutch firm, Oceans of Energy, alos revealed that it planned to turn an offshore seaweed farm in the North Sea into a large solar power farm over the next three years.'
        ),
        'source'=>array(
            'www.pv-tech.org',
            'https://www.pv-tech.org/news/sunseap-to-build-5mw-offshore-floating-solar-system-near-singapore/'
        ),
        'display'=>1
    ),
    array(
        'id'=>'2018_11_09_03',
        'datetime'=>'2018-11-09',
        'title'=>'Tesla and RGS set for solar roof tile market share battle in US',
        'content'=>array(
            '___TEXTS___Dual function residential roof tiles or shingles that incorporate solar PV (photovoltaic) cells into an integrated PV system for new build and retrofit homes has already received the ‘hype’ from Tesla’s Elon Musk but a new kid on the blog is set to go head to head with Tesla for this niche US market both from a manufacturing and installation perspective. The hype surrounding Tesla’s solar roof system seems to be very much US centric, not least because the product may take many years to travel across the Atlantic and gain a foothold in Europe, or Tesla may simply not bother as solar roof tile technology from a number of European companies has been around many years in Europe and remains a cool but niche product.
<br><br>
The hype surrounding Tesla’s solar roof system seems to be very much US centric, not least because the product may take many years to travel across the Atlantic and gain a foothold in Europe, or Tesla may simply not bother as solar roof tile technology from a number of European companies has been around many years in Europe and remains a cool but niche product.',
            '___IMAGE___001.jpg',
            '___TEXTS___PV Tech recently highlighted that Tesla’s solar roof tile product offering was still undergoing design iterations ahead of a volume manufacturing ramp at its Gigafactory 2 facility in Buffalo, New York state.
<br><br>
Having had false volume manufacturing starts in the past, Tesla expects the volume production ramp at Gigafactory 2 is expected to occur in the first half of 2019, compared to the previous expectations that the ramp would happen ‘near the end of 2018’.
<br><br>
It should be noted that Gigafactory 2, which is operated by partner Panasonic is already ramping conventional PV panel production for Tesla branded panels using Panasonic’s HIT (Heterojunction with Intrinsic Thin layer) solar cells and as PV Tech recently reported have been used in 70% of Tesla’s residential rooftop installations in California.
<br><br>
Tesla’s solar roof tiles have also adopted Panasonic’s HIT technology, not least because of the cells have some of the highest conversion efficiencies but in a building integrated PV (BIPV) system such as the roof tile system, they also have very low temperature coefficient of -0.258%/°C.
<br><br>
In basic terms, when the temperature of the cell increases, the conversion efficiency degrades.
<br><br>
In a BIPV application, cells can operate for longer periods at higher ambient temperatures than cells in a conventional retrofit rooftop solar panel system, which enables airflow beneath the panels, helping to reduce the temperature related performance degradation, something of an engineering challenge for BIPV applications using crystalline silicon solar cells.
<br><br>
Rival RGS Energy
<br><br>
Many people may not be aware of RGS Energy (Real Goods Solar Inc.), otherwise then known previously as RGS Solar, which is listed on NASDAQ (NASDAQ:RGSE) and was a ‘small cap’ residential and commercial solar installer based in Denver Colorado.
<br><br>
To put the ‘small cap’ into perspective, RGS had annual revenue in 2017 of a smidge over US$14 million.
<br><br>
However, on September 29, 2017, RGS entered into an exclusive deal with Dow Chemical to exclusively produce and sell a third generation (3.0) solar shingle technology under the ‘POWERHOUSE’ brand using conventional high-efficiency crystalline silicon solar cells rather than the original CIGS (Copper, Indium, Gallium, Selenide) thin-film substrates that were developed by Dow but the business was never successful and closed-down.',
            '___IMAGE___002.jpg',
            '___TEXTS___Dow Chemical only deployed around 1,000 POWERHOUSE installations across 18 US states, since its initial launch back in 2009.
<br><br>
RGS Energy is following the ‘fabless’ route by offloading all materials, manufacturing and assembly to subcontractors.
<br><br>
Major China-based PV manufacturer, Risen Energy is a key supplier with the solar cells and wire harness connectors for the next step of encapsulation into what RGS describes as a solar laminate.
<br><br>
At the end of 2017, Risen Energy had 6.6GW of solar panel assembly capacity at facilities primarily in China (Ningbo, Zhejiang, Luoyang, Henan, Wuhai, Inner Mongolia, Jiujiang and Jiangxi) but also in Mexico. The company is also an EVA encapsulant manufacturer with an annual production capacity of 230 million square meters, certified by TUV, VDE, CQC, JET and SGS as well as a track record in flexible component lamination processing.
<br><br>
Risen Energy completed a 2GW high-efficiency P-type monocrystalline solar cell plant in the first half of 2018, bringing total solar cell capacity to around 3.5GW.
<br><br>
The company reported revenue (operating income) of around US$1.8 billion in 2017 and had R&D expenditure of over US$56 million last year, the sixth highest spender according to PV Tech’s annual R&D spending analysis of 20 public listed PV manufacturers.',
            '___IMAGE___003.jpg',
            '___TEXTS___On the manufacturing front, Panasonic in comparison (to the best of our understanding) had a combined total cell and module capacity that is located in Japan, Malaysia and US of close to 1GW.
<br><br>
General Polymers Thermoplastic Materials, a multi-national thermoplastic resin distributor serving custom injection moulder’s in North America is to supply the polypropylene plastic resin for the base assembly of the RGS solar shingles.
<br><br>
Creative Liquid Coatings, which was a previous supplier to Dow is to provide moulded polymer components fully assembled, with all solar components, wire harnesses and other parts required to deliver a finished solar shingle product.
<br><br>
Later, in June, 2018, RGS announced that it had entered into a manufacturing agreement with Revere Plastic Systems, LLC, another injection moulding manufacturer, to bolster its supply chain to meet up to US$138 million in estimated annual POWERHOUSE revenue (more on that further down below).
<br><br>
At the beginning of August, 2018, RGS appointed Venture Global Solutions (VGS), a third party logistic (3PL) division of Venture Logistics, for the nationwide rollout in 50 US states as well as Canada. VGS is to provide 3PL services for the solar shingle system, which includes network optimization, warehousing and final packaging, according to the company.
<br><br>
Technically, RGS could have been in production of its solar shingle system in 2018, but the new system needed UL (Underwriters Laboratory) testing and certification (UL 1703).
<br><br>
Initially, RGS was hoping to gain certification in the first quarter of 2018 but then announced within an hour of receiving the UL mark, on November 2. (It should be noted that UL testing can take an unspecified amount time and should not be seen as a poor reflection on the POWERHOUSE 3.0 product).
<br><br>
“Receiving UL certification allows us to apply the UL Mark to our POWERHOUSE Solar Shingle System,” commented Dennis Lacey, RGS Energy’s CEO. “We will immediately begin manufacturing and taking purchase orders for POWERHOUSE nationwide.”
<br><br>
From prepared remarks in RGS’ recent third quarter earnings conference call, first product shipments could land in the US before the end of 2018. However, ‘volume’ production and system availability is expected from the first quarter of 2019 onwards.
<br><br>
RGS noted in recent press release that it actually expected revenue generation for the solar shingles in the first quarter of 2019.
<br><br>
“It is simply a matter of how quickly we can manufacture solar laminate, ship it from China, assemble it in the U.S. and then distribute POWERHOUSE kits over the remaining 50-plus days this year,” noted Dennis Lacey, CEO of RGS Energy in the earnings call. “Our stated view has been consistent on this topic. For instance, we have said it will be bumpy as we start to manufacture and balance product demand with our manufacturing capability. It will take a quarter or so to achieve an equilibrium source, but a good problem for us to have as we expect to be profitable".',
            '___IMAGE___004.jpg',
            '___TEXTS___Written reservations
<br><br>
Since announcing the POWERHOUSE licencing agreement with Dow Chemical, RGS has reflected the interest in the product, primarily from US roofing contractors, using the term ‘written reservations’.
<br><br>
These are not deposits, orders or guarantees of revenue generation, simply the interest from this sector but with the added factor of attributing a possible revenue generating figure from the written reservations the company has received before achieving UL certification.
<br><br>
On November 2, 2018 RGS Energy announced that it had received a total of US$126 million in written reservations from a total of 87 roofers across 32 US states. To date, written reservations were said to have exceed US$127 million in potential annual revenue, from 88 roofers across 32 US states.
<br><br>
The chart below is the number of statements RGS has made since May 2018 with the subsequent nominal revenue value of cumulative written reservations.
<br><br>
On November 2, 2018 RGS Energy announced that it had received a total of US$126 million in written reservations from a total of 87 roofers across 32 US states.',
            '___IMAGE___005.jpg',
            '___TEXTS___Battle begins
<br><br>
So the battle for solar tiles/shingles market share dominance in the US really kicks-off from the first quarter of 2019 as both Tesla and RGS start or get close to ramping to volume production.
<br><br>
Of course there is potentially a ‘no contest’ call as Tesla’s tiles are pegged at the luxury end of the housing market, due to technology, manufacturing and quality perceptions and costs, as well as and its target of its existing customer base from the EV sector.
<br><br>
RGS, we suspect would not turn that market away or dissuade its roofer customer base from such business but clearly the initial market is addressing residential homeowners with asphalt rooftops, which represent about 85% of US homes, according to RGS.
<br><br>
Both markets are potentially problematic as conventional solar panels dominate all residential markets around the world. Both companies are therefore competing with the biggest solar installers and panel manufacturers.
<br><br>
That said, Tesla’s hype over its system should not be underestimated even though it has been unwilling to disclose ‘reservations’ and ‘deposits’ unlike the pre-launch of its Model 3 electric vehicle.
<br><br>
Having two companies being public listed with two competing products in one country should make things more interesting and hopefully transparent. We could all soon be watching the birth of a new mainstream solar market or the trial and tribulations of a niche play.'
        ),
        'source'=>array(
            'www.pv-tech.org',
            'https://www.pv-tech.org/news/tesla-and-rgs-set-for-solar-roof-tile-market-share-battle-in-us/'
        ),
        'display'=>1
    )
);
?>