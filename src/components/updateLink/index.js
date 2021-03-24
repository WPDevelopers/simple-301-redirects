import React, { useState, useRef } from 'react';
const UpdateLink = ({ localClickHandler }) => {
	const [clicked, setClicked] = useState(false);
	const loadingDockRef = useRef(null);
	const buttonRef = useRef(null);
	const loadRef = useRef(null);
	const loadBRef = useRef(null);
	const checkRef = useRef(null);
	const handleClick = () => {
		localClickHandler('update');
		if (!clicked) {
			setClicked(true);
			buttonRef.current.classList.remove('Simple301Redirects__return');
			buttonRef.current.blur();
			loadingDockRef.current.classList.add('Simple301Redirects__loaded');
			loadRef.current.style.display = 'initial';
			loadBRef.current.style.display = 'initial';

			setTimeout(() => {
				loadRef.current.style.opacity = 1;
			}, 225);

			setTimeout(() => {
				loadBRef.current.style.opacity = 1;
			}, 350);

			setTimeout(() => {
				loadingDockRef.current.classList.remove('Simple301Redirects__loaded');
				loadRef.current.style.display = 'none';
				loadBRef.current.style.display = 'none';
				loadRef.current.style.opacity = 0;
				loadBRef.current.style.opacity = 0;
				buttonRef.current.classList.add('Simple301Redirects__popout');
				buttonRef.current.innerHTML = '';

				setTimeout(() => {
					checkRef.current.style.display = 'block';
				}, 150);
			}, 1450);
			setTimeout(() => {
				buttonRef.current.classList.remove('Simple301Redirects__popout');
				buttonRef.current.classList.add('Simple301Redirects__return');
				buttonRef.current.innerHTML = 'Update';
				checkRef.current.style.display = 'none';
				setClicked(false);
			}, 2000);
		}
	};

	return (
		<React.Fragment>
			<div className="Simple301Redirects__loading-dock" ref={loadingDockRef}>
				<svg id="Simple301Redirects__load-b" x="0px" y="0px" viewBox="0 0 150 150" ref={loadBRef}>
					<circle className="Simple301Redirects__loading-inner" cx={75} cy={75} r={60} />
				</svg>
				<svg id="Simple301Redirects__load" x="0px" y="0px" viewBox="0 0 150 150" ref={loadRef}>
					<circle className="Simple301Redirects__loading-inner" cx={75} cy={75} r={60} />
				</svg>
				<button className="Simple301Redirects__loading__button__submit" ref={buttonRef} onClick={() => handleClick()}>
					Update
				</button>
				<svg id="Simple301Redirects__check" style={{ width: '24px', height: '24px' }} viewBox="0 0 24 24" ref={checkRef}>
					<path fill="#FFFFFF" d="M9,20.42L2.79,14.21L5.62,11.38L9,14.77L18.88,4.88L21.71,7.71L9,20.42Z" />
				</svg>
			</div>
		</React.Fragment>
	);
};
export default UpdateLink;
