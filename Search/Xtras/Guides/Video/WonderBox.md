**WonderBox: Video Collection**

**Instructions**
1. Create category folders e.g. Cartoons
2. Add subfolders based on category e.g Scooby Doo
3. Add cover art and videos in respective folders 
4. Click on category to display cover art 
5. Use search function to filter collection 



```mermaid
graph TD
    WonderBox["WonderBox/"] --> folder1["Cartoons/"]
    WonderBox --> folder2["Music/"]
    WonderBox--> folder3["Series/"]

    folder1 --> sub1["Scooby Doo/"]

    sub1 --> scooby1["Scooby Doo.jpg"]
    sub1 --> scooby2["A Clue for Scooby-Doo.mp4"]
    sub1 --> scooby3["Hassle in the Castle.mp4"]

    folder2 --> sub2["Carpool Karaoke/"]

    sub2 --> carpool1["Carpool Karaoke.jpg"]
    sub2 --> carpool2["Adele.mp4"]
    sub2 --> carpool3["Britney Spears.mp4"]

    folder3 --> sub3["The Nanny/"]

    sub3 --> nanny1["The Nanny.jpg"]
    sub3 --> nanny2["Smoke Gets in Your Lies.mp4"]
    sub3 --> nanny3["My Fair Nanny.mp4"]



 




```