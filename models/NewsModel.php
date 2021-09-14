<?php

class NewsModel
{    
    function create($conn, $entity) 
    {
        try {
            $stmt = $conn->prepare(
                'INSERT INTO news(
                    headline, link, news_datetime, created_at
                ) 
                VALUES(
                    :headline, :link, :news_datetime, :created_at
                )'
            );
            
            $stmt->execute(
                array(
                    ':headline' => $entity['headline'],
                    ':link' => $entity['link'],
                    ':news_datetime' => $entity['news_datetime'],
                    ':created_at' => date('Y-m-d H:i:s'),
                )
            );

            return true;
        } catch(PDOException $e) {
            //echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    function existsNews($conn, $news_datetime, $headline) 
    {    
        $stmt = $conn->prepare(
            'SELECT id FROM news 
            WHERE news_datetime = :news_datetime
            AND headline = :headline'
        );
        $stmt->execute(
            array(
                ':news_datetime' => $news_datetime,
                ':headline' => $headline
            )
        );
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
}