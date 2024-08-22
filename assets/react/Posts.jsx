import React from "react";
import {createRoot} from "react-dom/client";

export default function Posts(){
    return <h2 style={{color:"black"}}>MIAROTIANA</h2>
}
class PostsElement extends HTMLElement {
    connectedCallback(){
        const root = createRoot(this)
        root.render(<Posts/>)
    }
}


customElements.define('test-react',PostsElement)