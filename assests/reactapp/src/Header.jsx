import React from 'react'
import {Container} from 'react-bootstrap'

/**
 * The component responsible of rendering page header
 */
function Header(props)
{
    return (
        <Container>
            <header className='d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom'>
                {props.children}
            </header>
        </Container>
    );
}

export default Header;
