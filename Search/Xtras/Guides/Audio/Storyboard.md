**Storyboard: The Story Player**

**Instructions**
1. Create category folders e.g. Darkness Within
2. Create Audio, Logo and Story folders
3. Add audio, logo and text in there respective folders
4. Click Load to see created folders and select one 
5. Click on any audio to start the narration (Click again to pause)
6. Click on story to read it 



```mermaid
graph TD
    Storyboard["Storyboard/"] --> folder1["Darkness Within/"]
    Storyboard --> folder2["Hidden Passion/"]
    Storyboard --> folder3["Dragonian Mind/"]

    folder1 --> sub1["Audio/"]
    folder1 --> sub2["Logo/"]
    folder1 --> sub3["Story/"]

    sub1 --> audio1["Chapter 1.mp3"]
    audio1 --> audio2["Chapter 2.mp3"]
    audio2 --> audio3["Chapter 3.mp3"]

    sub2 --> pic1["logo.jpg"]

    sub3 --> text1["Story.txt"]

    folder2 --> sub4["Audio/"]
    folder2 --> sub5["Logo/"]
    folder2 --> sub6["Story/"]

    sub4 --> audio4["Chapter 1.mp3"]
    audio4 --> audio5["Chapter 2.mp3"]
    audio5 --> audio6["Chapter 3.mp3"]

    sub5 --> pic2["logo.jpg"]

    sub6 --> text2["Story.txt"]

    folder3 --> sub7["Audio/"]
    folder3 --> sub8["Logo/"]
    folder3 --> sub9["Story/"]

    sub7 --> audio7["Chapter 1.mp3"]
    audio7 --> audio8["Chapter 2.mp3"]
    audio8 --> audio9["Chapter 3.mp3"]

    sub8 --> pic3["logo.jpg"]

    sub9 --> text3["Story.txt"]

```





    


