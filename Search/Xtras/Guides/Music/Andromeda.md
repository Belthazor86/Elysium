**Andromeda: Music Album and Playlist Player**

**Instructions**
1. Add albums to Andromeda folder
2. Use Load feature to retrieve albums
3. Use scan feature to display music from Andromeda folder
4. Click on album art to play album e.g Britney.jpg




```mermaid
graph TD
    Andromeda["Andromeda/"] --> folder1["Britney Spears/"]
    Andromeda --> folder2["Celine Dion/"]
    Andromeda --> folder3["Luther Vandross/"]

    folder1 --> sub1["Britney/"]

    sub1 --> britney1["Britney.jpg"]
    sub1 --> britney2["01 I'm A Slave 4 U.mp3"]
    sub1 --> britney3["02 Overprotected.mp3"]

    folder2 --> sub2["Let's Talk About Love/"]

    sub2 --> celine1["Let's Talk About Love.jpg"]
    sub2 --> celine2["01 The Reason.mp3"]
    sub2 --> celine3["02 Immortality.mp3"]

    folder3 --> sub3["The Ultimate Luther Vandross/"]

    sub3 --> luther1["The Ultimate Luther Vandross.jpg"]
    sub3 --> luther2["01 Never Too Much.mp3"]
    sub3 --> luther3["02 Take You Out.mp3"]


```



 


