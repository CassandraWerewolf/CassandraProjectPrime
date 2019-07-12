create or replace view Tally_display_lhv as SELECT game_id, day, votee, sum(if(unvoted='Yes',0,1)) as total, group_concat(if(unvoted='Yes',concat('[-]',concat(voter,'(',vote_count,')'),'[/-]'),concat(voter,if(nightfall='Yes','*',''),'(',vote_count,')')) order by vote_count separator ', ')as votes_bgg, group_concat(if(unvoted='Yes',concat('<strike>',concat(voter,'(',vote_count,')'),'</strike>'),concat(voter,if(nightfall='Yes','*',''),'(',vote_count,')')) order by vote_count separator ', ')as votes_html from Tally_votes group by game_id, day, votee order by game_id, day, if(votee='nightfall', null, 1) desc, total desc, min(if(unvoted='Yes',NULL,vote_count)) asc
