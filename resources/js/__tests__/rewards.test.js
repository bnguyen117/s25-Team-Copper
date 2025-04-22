import { fireEvent, screen } from "@testing-library/dom";
import "@testing-library/jest-dom";

document.body.innerHTML = `
    <a href="https://prod.s25-team-copper.stspreview.com/finance#goals" 
       class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center block"
       id="goal-button">
        + Add or Edit Goals
    </a>
`;

test("Finance Goals button exists and links correctly", () => {
    const button = screen.getByText("+ Add or Edit Goals");

    expect(button).toBeInTheDocument();
    expect(button).toHaveAttribute("href", "https://prod.s25-team-copper.stspreview.com/finance#goals");
});