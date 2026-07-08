**Gallerium: The Wallaper (Landscape) Collection**

**Instructions**
1. Create category folders e.g. Games
2. Add subfolders based on category e.g The Witcher
3. Add additional subfolders e.g 1,2,3
4. Add cover art and wallapers in additional subfolders  
5. Ensure the cover art is the first image in the subfolder e.g. 1. Wolverine or 0
6. Click on category or all button to display cover images 
7. Use search function to filter collection 



```mermaid
graph TD
    Gallerium["Gallerium/"] --> folder1["Games/"]
    Gallerium --> folder2["Movies/"]
    Gallerium --> folder3["Series/"]

    folder1 --> sub1["The Witcher/"]

    sub1 --> witcher1["1/"]
    sub1 --> witcher2["2/"]
    sub1 --> witcher3["3/"]

    witcher1 --> cover1["1.The Witcher.jpg"]
    cover1 --> wallpaper1["Wallpaper1.jpg"]
    wallpaper1 --> wallpaper2["Wallpaper2.jpg"]
    
    witcher2 --> cover2["2.The Witcher.jpg"]
    cover2 --> wallpaper3["Wallpaper3.jpg"]
    wallpaper3 --> wallpaper4["Wallpaper4.jpg"]

    witcher3 --> cover3["3.The Witcher.jpg"]
    cover3 --> wallpaper5["Wallpaper5.jpg"]
    wallpaper5 --> wallpaper6["Wallpaper6.jpg"]


    folder2 --> sub2["Wolverine/"]

    sub2 --> wolverine1["1/"]
    sub2 --> wolverine2["2/"]
    sub2 --> wolverine3["3/"]

    wolverine1 --> cover4["1.Wolverine.jpg"]
    cover4 --> wallpaper7["Wallpaper1.jpg"]
    wallpaper7 --> wallpaper8["Wallpaper2.jpg"]
    
    wolverine2 --> cover5["2.Wolverine.jpg"]
    cover5 --> wallpaper9["Wallpaper1.jpg"]
    wallpaper9 --> wallpaper10["Wallpaper2.jpg"]

    wolverine3 --> cover6["3.Wolverine.jpg"]
    cover6 --> wallpaper11["Wallpaper1.jpg"]
    wallpaper11 --> wallpaper12["Wallpaper2.jpg"]


    folder3 --> sub3["Bakugan Battle Brawlers/"]

    sub3 --> bakugan1["1/"]
    sub3 --> bakugan2["2/"]
    sub3 --> bakugan3["3/"]

    bakugan1 --> cover7["1.Bakugan.jpg"]
    cover7 --> wallpaper13["Wallpaper1.jpg"]
    wallpaper13 --> wallpaper14["Wallpaper2.jpg"]
    
    bakugan2 --> cover8["2.Bakugan.jpg"]
    cover8 --> wallpaper15["Wallpaper1.jpg"]
    wallpaper15 --> wallpaper16["Wallpaper2.jpg"]

    bakugan3 --> cover9["3.Bakugan.jpg"]
    cover9 --> wallpaper17["Wallpaper1.jpg"]
    wallpaper17 --> wallpaper18["Wallpaper2.jpg"]

 




```
