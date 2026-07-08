**Vetra: Video Collection**

**Instructions**
1. Create category folders e.g. Animation 
2. Add subfolders based on category e.g The Witcher
3. Add additional subfolders e.g 1,2,3
4. Add cover and wallaper in additional subfolders  
5. Ensure the cover is the first image in the subfolder e.g. 1. Wolverine or 0
6. Click on category or all button to display cover images 
7. Use search function to filter collection 



```mermaid
graph TD
    Vetra["Vetra/"] --> folder1["Animation/"]
    Vetra --> folder2["Games/"]
    Vetra--> folder3["Movies/"]

    folder1 --> sub1["The Last Airbender/"]

    sub1 --> ATLA1["1/"]
    sub1 --> ATLA2["2/"]
    sub1 --> ATLA3["3/"]

    ATLA1 --> cover1["1.ATLA.jpg"]
    cover1 --> video1["1. Aang vs Zuko.mp4"]
    video1 --> video2["2. Aang vs King Bumi.mp4"]
    
    ATLA2 --> cover2["2.ATLA.jpg"]
    cover2 --> video3["1. Aang vs General Fong.mp4"]
    video3 --> video4["2. Team Avatar vs Swamp Monster.mp4"]

    ATLA3 --> cover3["3.ATLA.jpg"]
    cover3 --> video5["1. The Painted Lady vs Fire Nation Soldiers.mp4"]
    video5 --> video6["2. Avatar Roku vs Fire Lord Sozin.mp4"]


    folder2 --> sub2["Dark Souls/"]

    sub2 --> darksouls1["1/"]
    sub2 --> darksouls2["2/"]
    sub2 --> darksouls3["3/"]

    darksouls1 --> cover4["1.Dark Souls.jpg"]
    cover4 --> video7["Intro.mp4"]
    video7 --> video8["Boss Battle.mp4"]
    
    darksouls2 --> cover5["2.Dark Souls.jpg"]
    cover5 --> video9["Intro.mp4"]
    video9 --> video10["Boss Battle.mp4"]

    darksouls3 --> cover6["3.Dark Souls.jpg"]
    cover6 --> video11["Intro.mp4"]
    video11 --> video12["Boss Battle.mp4"]


    folder3 --> sub3["Wolverine/"]

    sub3 --> wolverine1["1/"]
    sub3 --> wolverine2["2/"]
    sub3 --> wolverine3["3/"]

    wolverine1 --> cover7["1.Wolverine.jpg"]
    cover7 --> video13["1. Wolverine vs. Blob.mp4"]
    video13 --> video14["2. Wolverine vs Sabretooth.mp4"]
    
    wolverine2 --> cover8["2.Wolverine.jpg"]
    cover8 --> video15["1. Wolverine meets Yukio.mp4"]
    video15 --> video16["2. Wolverine saves Mariko.mp4"]

    wolverine3 --> cover9["3.Wolverine.jpg"]
    cover9 --> video17["1. Logan Meet Laura.mp4"]
    video17 --> video18["2. Wolverine takes The Serum.mp4"]

 




```