import React, { useEffect, useRef, useState } from 'react'
import { useSearchParams } from 'react-router-dom';
import GameHeading from '../components/mobile/GameHeading';
import GameLists from '../components/mobile/GameLists';
import demo from '../assets/img/demo.png';
import bg from '../assets/game.mp3'

const DemoPlayPage = () => {
  const [searchParams]=useSearchParams();
  const [games,setGames]=useState([]);
  const gameType=searchParams.get('type');
  const gameList=searchParams.get('list');
  const gamesData=[
     {
        type:'slot',
        lists:[
            {
                name:'JILI',
                games:[
                    'https://nctmedia.online/gamelist/jili_gamelist/263_en.webp',
                    'https://nctmedia.online/gamelist/jili_gamelist/420_en.webp',
                    'https://nctmedia.online/gamelist/jili_gamelist/299_en.webp',
                    'https://nctmedia.online/gamelist/jili_gamelist/399_en.webp'
                ]
              },
            {
              name:'PP',
              games:[
                  'https://nctmedia.online/gamelist/pp_gamelist/PP-SLOT-464_en.webp',
                  'https://nctmedia.online/gamelist/pp_gamelist/PP-SLOT-465_en.webp',
                  'https://nctmedia.online/gamelist/pp_gamelist/PP-SLOT-466_en.webp',
                  'https://nctmedia.online/gamelist/pp_gamelist/PP-SLOT-467_en.webp'
              ]
            } ,
            {
              name:'PG',
              games:[
                  'https://nctmedia.online/gamelist/pg_gamelist/1623475_anubis-wrath_en.webp',
                  'https://nctmedia.online/gamelist/pg_gamelist/1717688_mystic-potion_en.webp',
                  'https://nctmedia.online/gamelist/pg_gamelist/1492288_pinata-wins_en.webp',
                  'https://nctmedia.online/gamelist/pg_gamelist/1508783_wild-ape_en.webp'
              ]
            },
            {
              name:'CQ9',
              games:[
                  'https://nctmedia.online/gamelist/cq9_gamelist/GB16_en.webp',
                  'https://nctmedia.online/gamelist/cq9_gamelist/243_en.webp',
                  'https://nctmedia.online/gamelist/cq9_gamelist/242_en.webp',
                  'https://nctmedia.online/gamelist/cq9_gamelist/GB15_en.webp'
              ]
            },
            {
              name:'JDB',
              games:[
                  'https://nctmedia.online/gamelist/jdb_gamelist/14088_en.webp',
                  'https://nctmedia.online/gamelist/jdb_gamelist/14090_en.webp',
                  'https://nctmedia.online/gamelist/jdb_gamelist/14091_en.webp',
                  'https://nctmedia.online/gamelist/jdb_gamelist/14089_en.webp'
              ]
            },
             {
              name:'JOKER',
              games:[
                  'https://nctmedia.online/gamelist/joker_gamelist/zcw3utgfzk75o.webp',
                  'https://nctmedia.online/gamelist/joker_gamelist/htacf8c11qejn.webp',
                  'https://nctmedia.online/gamelist/joker_gamelist/bqc117dipjiso.webp',
                  'https://nctmedia.online/gamelist/joker_gamelist/g58bao4yefdrq.webp'
              ]
            },
            {
              name:"PS",
              games:[
                  'https://nctmedia.online/gamelist/ps_gamelist/PSS-ON-00156_en.webp',
                  'https://nctmedia.online/gamelist/ps_gamelist/PSS-ON-00155_en.webp',
                  'https://nctmedia.online/gamelist/ps_gamelist/PSS-ON-00151_en.webp',
                  'https://nctmedia.online/gamelist/ps_gamelist/PSS-ON-00154_en.webp'
              ]
            },
            {
              name:'KA',
              games:[
                  'https://nctmedia.online/gamelist/ka_gamelist/FatGuy_en.webp',
                  'https://nctmedia.online/gamelist/ka_gamelist/StoryOfFarmer_en.webp',
                  'https://nctmedia.online/gamelist/ka_gamelist/ChihuahuaParty_en.webp',
                  'https://nctmedia.online/gamelist/ka_gamelist/HailTheJudge_en.webp'
              ]
            },
           ]
    },
    {
        type:'fishing',
        lists:[
            {
                name:'JILI',
                games:[
                    'https://nctmedia.online/gamelist/jili_gamelist/289_en.webp',
                    'https://nctmedia.online/gamelist/jili_gamelist/212_en.webp',
                    'https://nctmedia.online/gamelist/jili_gamelist/153_en.webp',
                    'https://nctmedia.online/gamelist/jili_gamelist/119_en.webp'
                ]
            },
            {
                name:'PS',
                games:[
                    'https://nctmedia.online/gamelist/ps_gamelist/PSF-ON-00002_en.webp',
                    'https://nctmedia.online/gamelist/ps_gamelist/PSF-ON-00006_en.webp',
                    'https://nctmedia.online/gamelist/ps_gamelist/PSF-ON-00001_en.webp'
                ]
            },
            {
                name:'JOKER',
                games:[
                    'https://nctmedia.online/gamelist/joker_gamelist/cz3wgrounyetc.webp',
                    'https://nctmedia.online/gamelist/joker_gamelist/xq9ohbyf9m79o.webp'
                ]
            },
            {
                name:'JDB',
                games:[
                    'https://nctmedia.online/gamelist/jdb_gamelist/7008_en.webp',
                    'https://nctmedia.online/gamelist/jdb_gamelist/7009_en.webp',
                    'https://nctmedia.online/gamelist/jdb_gamelist/7007_en.webp'
                ]
            },
            {
                name:'CQ9',
                games:[
                    'https://nctmedia.online/gamelist/cq9_gamelist/GO02_en.webp',
                    'https://nctmedia.online/gamelist/cq9_gamelist/AT05_en.webp',
                    'https://nctmedia.online/gamelist/cq9_gamelist/AT01_en.webp'
                ]
            },
            {
                name:'KA',
                games:[
                    'https://nctmedia.online/gamelist/ka_gamelist/LeprechaunAndAnimals_en.webp',
                    'https://nctmedia.online/gamelist/ka_gamelist/MonsterDestroyer_en.webp'
                ]
            },
            {
                name:'YB',
                games:[
                    'https://nctmedia.online/gamelist/yb_gamelist/4003_en.webp',
                    'https://nctmedia.online/gamelist/yb_gamelist/2001_en.webp'
                ]
            },
         ]
    },
    {
        type:'live casino',
        lists:[
            {
                name:'JILI',
                games:[
                   'https://nctmedia.online/gamelist/jili_gamelist/407_en.webp',
                   'https://nctmedia.online/gamelist/jili_gamelist/262_en.webp'
                ]
            },
            {
                name:'SA',
                games:[
                  'https://nctmedia.online/gamelist/sa_baccarat/840.webp',
                  'https://nctmedia.online/gamelist/sa_baccarat/866.webp'
                ]
            },
            {
                name:'JOKER',
                games:[
                  'https://nctmedia.online/gamelist/joker_gamelist/856dgq3a8r9d6.webp',
                  'https://nctmedia.online/gamelist/joker_gamelist/j3wngk3efrzn6.webp'
                ]
            },
            {
                name:'SEXY',
                games:[
                  'https://nctmedia.online/gamelist/sexy_baccarat/MX-LIVE-013.webp',
                  'https://nctmedia.online/gamelist/sexy_baccarat/MX-LIVE-017.webp'
                ]
            },
            {
                name:'AG',
                games:[
                 'https://nctmedia.online/gamelist/ag_gamelist/0.webp',
                 'https://nctmedia.online/gamelist/ag_gamelist/27.webp'
                ]
            },
            {
                name:'CQ9',
                games:[
                 'https://nctmedia.online/gamelist/cq9_gamelist/GO03_en.webp',
                 'https://nctmedia.online/gamelist/cq9_gamelist/BT03_en.webp'
                ]
            },
        ]
  },
    {
        type:'sport book',
        lists:[
          {
            games:[
                'https://shwedinker777.online/assets/img/game_logo/sbo_sport.jpeg',
    'https://shwedinker777.online/assets/img/game_logo/ug_sport.jpeg',
    'https://shwedinker777.online/assets/img/game_logo/ibc.jpeg',
                'https://shwedinker777.online/assets/img/game_logo/ssport.jpeg'
            ]
          }
        ]
    }
]
useEffect(()=>{
 if(gameType==='sport book') {
    setGames(
          {
            games:[
                'https://maxwinapi.online/assets/img/game_logo/sbo_sport.jpeg',
    'https://maxwinapi.online/assets/img/game_logo/ssport.jpeg',
             ]
          }
        
    )
 }else{
    const selectedGameType=gamesData.filter((games)=>games.type===gameType)[0];
    console.log('selectedGameType',selectedGameType)
    const selectedGameList=selectedGameType?.lists?.filter((list)=>list.name===gameList)[0];
setGames(selectedGameList);
 }
},[gameType,gameList])
const audioRef=useRef(null);

 
  return (
    <div  style={{overflowX:'hidden'}} className='gamesBg'>
     {/* <GameHeading/> */}
       <img src={demo} className='demoImg' />
       <GameLists/>
        <div className="py-4 px-2 px-sm-4">
       <h4 className="fw-bold text-white ms-2">
       {searchParams.get('type')?.toUpperCase()} {searchParams.get('list')}
      </h4>
              <div className="ms-2 row mt-3 mb-5 mx-auto">
                 {games?.games?.map((item,index)=>{
                    return <div key={index} className='p-0 cursor-pointer col-4 col-sm-3 col-lg-2 mb-2 mb-sm-3'>
                         <img src={item} className='gameImg  rounded-3 ' />
                    </div>
                })}
                </div>
                </div>
    </div>
  )
}

export default DemoPlayPage
