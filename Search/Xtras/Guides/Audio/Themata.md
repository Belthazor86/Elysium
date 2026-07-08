**Themata: The Theme Player**

**Instructions**
1. Create category folders e.g. Games
2. Add subfolders based on category e.g. Dark Souls 
3. Add additional subfolders e.g. 1,2,3
4. Add audio, cover and wallaper in additional subfolders  



```mermaid
graph TD
    Themata["Themata/"] --> folder1["Games/"]
    Themata --> folder2["Movies/"]
    Themata --> folder3["Series/"]

    folder1 --> sub1["Dark Souls/"]

    sub1 --> darksouls1["1/"]
    sub1 --> darksouls2["2/"]
    sub1 --> darksouls3["3/"]

    darksouls1 --> audio1["Audio.mp3"]
    audio1 --> cover1["Cover.jpg"]
    cover1 --> wallpaper1["Wallaper.jpg"]
    
    darksouls2 --> audio2["Audio.mp3"]
    audio2 --> cover2["Cover.jpg"]
    cover2 --> wallpaper2["Wallaper.jpg"]

    darksouls3 --> audio3["Audio.mp3"]
    audio3 --> cover3["Cover.jpg"]
    cover3 --> wallpaper3["Wallaper.jpg"]

    folder2 --> sub2["The Lord of the Rings/"]

    sub2 --> LOTR1["1/"]
    sub2 --> LOTR2["2/"]
    sub2 --> LOTR3["3/"]

    LOTR1 --> audio4["Audio.mp3"]
    audio4 --> cover4["Cover.jpg"]
    cover4 --> wallpaper4["Wallaper.jpg"]
    
    LOTR2 --> audio5["Audio.mp3"]
    audio5 --> cover5["Cover.jpg"]
    cover5 --> wallpaper5["Wallaper.jpg"]

    LOTR3 --> audio6["Audio.mp3"]
    audio6 --> cover6["Cover.jpg"]
    cover6 --> wallpaper6["Wallaper.jpg"]


    folder3 --> sub3["Bakugan Battle Brawlers/"]

    sub3 --> Bakugan1["1/"]
    sub3 --> Bakugan2["2/"]
    sub3 --> Bakugan3["3/"]
    sub3 --> Bakugan4["4/"]

    Bakugan1 --> audio7["Audio.mp3"]
    audio7 --> cover7["Cover.jpg"]
    cover7 --> wallpaper7["Wallaper.jpg"]
    
    Bakugan2 --> audio8["Audio.mp3"]
    audio8 --> cover8["Cover.jpg"]
    cover8 --> wallpaper8["Wallaper.jpg"]

    Bakugan3 --> audio9["Audio.mp3"]
    audio9 --> cover9["Cover.jpg"]
    cover9 --> wallpaper9["Wallaper.jpg"]

    Bakugan4 --> audio10["Audio.mp3"]
    audio10 --> cover10["Cover.jpg"]
    cover10 --> wallpaper10["Wallaper.jpg"]




```
