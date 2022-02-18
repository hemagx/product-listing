import React, {Component} from 'react'
import {Container} from 'react-bootstrap'

/**
 * The component responsible of rendering page footer
 */
class Footer extends Component {
    render() {
        return (
            <Container>
                <footer className='py-3 my-4 border-top'>
                    <p className='text-muted text-center'>Simple Product Listing App</p>
                </footer>
            </Container>
        )
    }
}

export default Footer;
